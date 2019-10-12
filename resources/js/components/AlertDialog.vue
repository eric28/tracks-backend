<template>
    <v-dialog v-model="dialog" persistent>
        <v-card>
            <v-card-title class="title">
                <div>
                    <v-alert
                            :value="true"
                            :type="type"
                            v-html="title"
                    >
                    </v-alert>
                </div>
            </v-card-title>
            <v-card-text class="content" v-html="message"></v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="close">Cerrar</v-btn>
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