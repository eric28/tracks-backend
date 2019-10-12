<template>
    <v-app id="inspire">
        <v-toolbar color="#00858A" app>
            <v-toolbar-title class="white--text">
                Listado de GPX
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items>
                <v-btn :disabled="loading" icon dark @click="loadGrid()">
                    <i class="material-icons md-18">replay</i>
                </v-btn>
                <add-gpx-button style="height: 100%" @added="loadGrid"></add-gpx-button>
            </v-toolbar-items>
        </v-toolbar>
        <v-content>
            <v-card>
                <v-container
                        fluid
                        grid-list-lg
                >
                    <v-layout row wrap>
                        <v-flex xs12 v-for="item in list" :key="item.id">
                            <v-card>
                                <v-card-title primary-title>
                                    <div>
                                        <span class="headline">{{item.name}}</span>
                                    </div>
                                </v-card-title>
                                <v-divider light></v-divider>
                                <l-map :zoom="11" :center="item.center_json" style="min-height: 300px;z-index: 1">
                                    <l-tile-layer :url="url"></l-tile-layer>
                                    <l-polyline
                                            :lat-lngs="item.gpx_json"
                                            color="green">
                                    </l-polyline>
                                    <l-circle
                                            :lat-lng="item.gpx_json[0]"
                                            :radius="20"
                                            color="blue">
                                    </l-circle>
                                    <l-circle
                                            :lat-lng="item.gpx_json[item.gpx_json.length -1 ]"
                                            :radius="20"
                                            color="red">
                                    </l-circle>
                                </l-map>
                                <v-sparkline
                                        :value="generateLine(item.gpx_json)"
                                        :padding="0"
                                        :gradient="['#F5B041', '#F4D03F', '#58D68D']"
                                        :line-width="0"
                                        :fill="true"
                                        auto-draw
                                ></v-sparkline>
                                <v-divider light></v-divider>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn flat color="red" @click="remove(item.id)">Borrar</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-flex>
                    </v-layout>

                </v-container>
            </v-card>
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
            this.loadGrid();
        },
        watch: {
            pagination: {
                handler() {
                    this.loadGrid();
                },
                deep: true
            },
        },
        methods: {
            generateQuery() {
                let params = {};
                let sort = this.pagination.sortBy;
                if (sort !== null) {
                    if (this.pagination.descending) sort = `-${sort}`;
                    params.sort = sort;
                }

                return params;
            },
            loadGrid() {
                this.loading = true;

                let params = this.generateQuery();
                params.length = this.pagination.rowsPerPage;
                params.start = (this.pagination.page - 1) * this.pagination.rowsPerPage;

                let url = 'gpx-listing';
                HttpHelper.get(url, params)
                    .then(projects => {
                        this.list = projects.data;
                        this.totalItems = projects.recordsTotal;
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
            }
        }
    }
</script>

<style scoped>
    .state {
        color: white;
        font-weight: bold;
        text-transform: capitalize;
    }

    .pig-info {
        cursor: help
    }
</style>