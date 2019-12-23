<template>
    <div>
        <alert-dialog v-model="alert.show" :type="alert.type" :message="alert.message" @close="closeAlert">
        </alert-dialog>

        <v-btn class="white--text" slot="activator" :icon="esMovil" :text="!esMovil" @click="dialog=true">
            <div v-show="!esMovil">Añadir</div>
            <i class="material-icons md-18">add</i>
        </v-btn>

        <v-dialog v-model="dialog" :fullscreen="esMovil" max-width="500px" scrollable persistent
                  transition="dialog-bottom-transition">
            <v-card>
                <v-card-title class="primary white--text">
                    Añadir GPX
                </v-card-title>
                <v-card-text class="pt-2">
                    <v-text-field label="Nombre" v-model="gpx.name"/>
                    <v-btn color="primary" :disabled="loading" @click="clickFileUpload()" v-html="fileName"/>

                    <input style="display: none" class="form-control" ref="fileupload" type="file" accept=".gpx"
                           @change="changeGPX($event)"/>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="error" text @click="closeModal()">Cerrar</v-btn>
                    <v-spacer/>
                    <v-btn color="primary" :loading="loading" :disabled="!canAdd() || loading" text @click="addGPX()">
                        Añadir
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    import AlertDialog from "../../../components/AlertDialog";
    import {HttpHelper} from "../../../commons/helpers/HttpHelper";

    export default {
        name: "AddGPXButton",
        components: {
            AlertDialog
        },
        data: () => ({
            loading: false,
            dialog: false,
            gpx: {
                name: null,
                file: null
            },
            file: null,
            alert: {
                show: false,
                type: "success",
                message: "alert"
            }
        }),
        computed: {
            fileName: function () {
                if (this.file == null) return "Selecciona un fichero .gpx";

                if (this.file.name.length > 20) return `${this.file.name.substring(0, 20)}...`;

                return this.file.name;
            },
            esMovil() {
                return this.$vuetify.breakpoint.smAndDown;
            }
        },
        methods: {
            changeGPX() {
                let fileReader = new FileReader();

                fileReader.addEventListener("load", (e) => {
                    this.gpx.file = e.target.result;
                });

                if (typeof event.target.files[0] != "undefined") {
                    this.file = event.target.files[0];
                    if (this.gpx.name == null || this.gpx.name === "")
                        this.gpx.name = this.file.name.replace(".gpx", "");
                    fileReader.readAsDataURL(this.file);
                } else {
                    this.file = null;
                    this.gpx.file = null;
                }
            },
            canAdd() {
                if (this.gpx.file == null || this.gpx.file.length < 30) return false;

                return this.gpx.name != null && this.gpx.name.length > 3;
            },
            addGPX() {
                this.loading = true;

                let url = 'gpx-add';
                HttpHelper.post(url, this.gpx)
                    .then((response) => {
                        this.$emit('imported');
                        this.alert.type = "success";
                        this.alert.message = "Track añadido correctamente";
                        this.alert.show = true;
                        this.closeModal();
                        this.$emit("added", response);
                    })
                    .catch((error) => {
                        this.alert.message = typeof error.message !== "undefined" ? error.message : error;
                        this.alert.type = "error";
                        this.alert.show = true;
                    })
                    .finally(() => {
                        this.loading = false;
                    });

            },
            clickFileUpload() {
                const input = this.$refs.fileupload;
                input.click();
            },
            closeModal() {
                this.resetInput();
                this.gpx.name = null;
                this.gpx.file = null;
                this.dialog = false;
            },
            closeAlert() {
                this.alert.show = false;
            },
            resetInput() {
                const input = this.$refs.fileupload;
                input.type = 'text';
                input.type = 'file';
                this.file = null;
            }
        }
    }
</script>

<style scoped>
</style>
