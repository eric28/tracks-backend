<template>
    <v-app id="inspire">
        <v-app-bar color="primary" app>
            <v-toolbar-title class="white--text">
                Listado de GPX
            </v-toolbar-title>
            <v-spacer/>
            <v-btn :disabled="loading" class="white--text" :loading="loading" :icon="esMovil" :text="!esMovil"
                   @click="loadGrid()">
                <div v-show="!esMovil">Recargar</div>
                <i class="material-icons md-18">replay</i>
            </v-btn>
            <add-gpx-button @added="loadGrid"/>
        </v-app-bar>

        <v-content class="grey">
            <v-container id="list-container">

                <v-row xs12 v-if="list.length < 1 && !loading" align="center" justify="center">
                    <v-col cols="12">
                        <v-alert color="primary" type="info" prominent>
                            <v-row align="center">
                                <v-col class="grow">Todavía no se ha añadido ningún track</v-col>
                                <v-col class="shrink">
                                    <add-gpx-button class="primary" @added="loadGrid"/>
                                </v-col>
                            </v-row>
                        </v-alert>
                    </v-col>
                </v-row>

                <v-row xs12 v-for="item in list" :key="item.id">
                    <v-col cols="12">
                        <v-card>
                            <v-card-title class="primary white--text headline" primary-title>
                                {{formatTitle(item.name)}}
                            </v-card-title>
                            <v-card-text>
                                <v-row>
                                    <v-col class="font-weight-bold">
                                        {{item.name}}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col :cols="esMovil ? 12 : 6">
                                        <v-icon class="primary--text">mdi-map-marker-distance</v-icon>
                                        &nbsp;<span class="font-weight-bold">Distancia:</span>
                                        &nbsp;{{formatDistance(item.distance)}}
                                    </v-col>
                                    <v-col :cols="esMovil ? 12 : 6">
                                        <v-icon class="red--text">mdi-trending-up</v-icon>
                                        &nbsp;<span class="font-weight-bold">Desnivel positivo:</span>
                                        &nbsp;{{formatUnevenness(item.unevenness_positive)}}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="pa-0 col-map">
                                        <l-map :zoom="11" :center="pointToLeaflet(item.center_json)" :style="styleMap">
                                            <l-tile-layer :url="url"/>
                                            <l-polyline
                                                :lat-lngs="pointsToLeaflet(item.gpx_json)"
                                                color="green">
                                            </l-polyline>
                                            <l-circle
                                                :lat-lng="pointToLeaflet(item.gpx_json[0])"
                                                :radius="20"
                                                color="blue">
                                            </l-circle>
                                            <l-circle
                                                :lat-lng="pointToLeaflet(item.gpx_json[item.gpx_json.length -1])"
                                                :radius="20"
                                                color="red">
                                            </l-circle>
                                        </l-map>
                                        <v-sparkline
                                            :style="styleElevation"
                                            :value="generateLine(item.gpx_json)"
                                            :padding="0"
                                            :gradient="['#F5B041', '#F4D03F', '#58D68D']"
                                            :line-width="0"
                                            :fill="true"
                                            auto-draw
                                        />
                                    </v-col>
                                </v-row>
                                <v-divider light/>
                            </v-card-text>
                            <v-divider light/>
                            <v-card-actions>
                                <v-spacer/>
                                <v-btn text color="red" @click="remove(item.id)">Borrar</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>

                <v-row xs12 v-show="loading" v-for="i in [0,1,2]" :key="i">
                    <v-col cols="12">
                        <v-skeleton-loader class="mx-auto grey darken-2" type="card-heading"/>
                        <v-skeleton-loader class="mx-auto" type="card"/>
                    </v-col>
                </v-row>

                <div v-intersect="nextPage"></div>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
    import {HttpHelper} from "../../commons/helpers/HttpHelper";
    import AddGpxButton from "./components/AddGPXButton";
    import {LCircle, LMap, LMarker, LPolyline, LTileLayer} from 'vue2-leaflet';

    export default {
        name: "ListingGpxPage",
        components: {
            AddGpxButton,
            LMap,
            LTileLayer,
            LMarker,
            LPolyline,
            LCircle
        },
        computed: {
            esMovil() {
                return this.$vuetify.breakpoint.smAndDown;
            },
            styleMap() {
                return {
                    'min-height': this.esMovil ? '30vh' : '40vh',
                    'z-index': 1
                }
            },
            styleElevation() {
                return {
                    "position": "absolute",
                    "bottom": -1,
                    "left": 0,
                    "z-index": 2,
                    "width": this.esMovil ? "60%" : "40%",
                    "background": "#0000001a"
                }
            }
        },
        data() {
            return {
                loading: true,
                pagination: {
                    page: 1,
                    rowsPerPage: 8,
                    sortBy: "-id"
                },
                totalItems: 0,
                list: [],
                dialogDetails: {
                    show: false,
                    id: null
                },
                url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
            }
        },
        mounted() {
            this.loadPage();
        },
        watch: {
            pagination: {
                handler() {
                    this.loadPage();
                },
                deep: true
            },
        },
        methods: {
            formatTitle(title) {
                if (title.length > 20) return `${title.substring(0, 20)}...`;
                return title;
            },
            generateQuery() {
                let params = {};
                let sort = this.pagination.sortBy;
                if (sort !== null) {
                    if (this.pagination.descending) sort = `-${sort}`;
                    params.sort = sort;
                }

                return params;
            },
            nextPage() {
                if (this.loading) return;
                if (this.totalItems <= this.pagination.rowsPerPage * this.pagination.page) return;
                this.pagination.page += 1;
            },
            loadGrid() {
                this.$vuetify.goTo('#list-container', {
                    duration: 1,
                    offset: 0,
                    easing: 'linear'
                });
                this.loading = true;
                this.list = [];
                if (this.pagination.page === 1) {
                    this.loadPage();
                } else {
                    this.pagination.page = 1;
                }
            },
            loadPage() {
                this.loading = true;

                let params = this.generateQuery();
                params.per_page = this.pagination.rowsPerPage;
                params.page = this.pagination.page;

                let url = 'gpx-listing';
                HttpHelper.get(url, params)
                    .then(projects => {
                        this.list = this.list.concat(projects.data);
                        this.totalItems = projects.total;
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
            generateLine(gpx) {
                let array = [];
                gpx.forEach((e) => {
                    array.push(parseFloat(e.elevation))
                });
                return array;
            },
            remove(id) {
                this.loading = true;
                this.list = [];

                let url = `gpx-remove/${id}`;
                HttpHelper.get(url)
                    .then(() => {
                        this.loadGrid();
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
            openDetails(id) {
                this.dialogDetails.id = id;
                this.dialogDetails.show = true;
            },
            closeDetails(id) {
                this.dialogDetails.id = null;
                this.dialogDetails.show = false;
                this.loadGrid();
            },
            openExternal(url) {
                window.open(url, '_blank');
            },
            pointsToLeaflet(points) {
                let arr = [];
                points.forEach((point) => {
                    arr.push(this.pointToLeaflet(point));
                });
                return arr;
            },
            pointToLeaflet(point) {
                return {lat: point.latitude, lon: point.longitude};
            },
            formatDistance(distance) {
                let distanceInKm = distance / 1000;
                return `${(Math.round((distanceInKm) * 100) / 100).toFixed(1)} Km`;
            },
            formatUnevenness(unevenness) {
                return `${(Math.round(unevenness * 100) / 100).toFixed(0)} m`;
            }
        }
    }
</script>

<style scoped>
    .col-map {
        position: relative;
    }
</style>
