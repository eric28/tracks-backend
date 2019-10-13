<template>
    <div>
        <alert-dialog v-model="alert.show" :type="alert.type" :message="alert.message" @close="closeAlert">
        </alert-dialog>

        <v-btn slot="activator" icon dark @click="dialog=true">
            <i class="material-icons md-18">add</i>
        </v-btn>

        <v-dialog v-model="dialog" max-width="500px" scrollable persistent transition="dialog-bottom-transition">
            <v-card>
                <v-card-title class="grey lighten-2">
                    Añadir GPX
                </v-card-title>
                <v-card-text>
                    <v-text-field label="Nombre" v-model="gpx.name"></v-text-field>
                    <v-btn color="primary" :disabled="loading" @click="clickFileUpload()" v-html="fileName"></v-btn>

                    <input style="display: none" class="form-control" ref="fileupload" type="file" accept=".gpx"
                           @change="changeGPX($event)"/>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="error" text @click="dialog=false">Cerrar</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" :loading="loading" :disabled="loading" text @click="addGPX()">
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
                else return this.file.name;
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
            addGPX() {
                this.loading = true;

                let url = 'gpx-add';
                HttpHelper.post(url, this.gpx)
                    .then((response) => {
                        this.$emit('imported');
                        this.alert.type = "success";
                        this.alert.message = "Añadido correctamente";
                        this.alert.show = true;
                        this.dialog = false;
                        this.$emit("added", response);
                    })
                    .catch((error) => {
                        this.alert.message = typeof error.mensaje !== "undefined" ? error.mensaje : error;
                        this.alert.type = "error";
                        this.alert.show = true;
                    })
                    .finally(() => {
                        this.loading = false;
                        this.gpx.name = null;
                        this.gpx.file = null;
                        this.resetInput();
                    });

            },
            clickFileUpload() {
                const input = this.$refs.fileupload;
                input.click();
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
