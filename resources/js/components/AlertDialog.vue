<template>
    <v-dialog :max-width="!esMovil ? '500px': undefined" v-model="dialog" persistent>
        <v-card>
            <v-card-title class="title">
                {{title}}
            </v-card-title>
            <v-card-text class="content">
                <v-alert :value="true" :type="type" prominent>
                    <div v-html="message"></div>
                </v-alert>
            </v-card-text>
            <v-card-actions>
                <v-spacer/>
                <v-btn text @click="close">Cerrar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        name: "AlertDialog",
        components: {},
        model: {
            prop: 'show',
            event: 'change'
        },
        props: {
            show: {type: Boolean},
            type: {type: String, required: true},
            message: {type: String | null, required: true},
        },
        data: () => ({
            dialog: false,
        }),
        mounted() {
            this.dialog = this.show;
        },
        computed: {
            title: function () {
                switch (this.type) {
                    case "success":
                        return "Satisfactorio";
                    case "error":
                        return "Error";
                    default:
                        return "Advertencia";
                }
            },
            esMovil() {
                return this.$vuetify.breakpoint.smAndDown;
            }
        },
        watch: {
            show: function (val) {
                this.dialog = val;
            }
        },
        methods: {
            close() {
                this.$emit('close');
            }
        }
    }
</script>

<style scoped>
    .title {
        display: block;
        padding: 0;
    }

    .content {
        max-height: 300px;
        overflow: auto;
    }
</style>
