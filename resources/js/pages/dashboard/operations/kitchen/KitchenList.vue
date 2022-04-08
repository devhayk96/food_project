<template>
    <div class="d-flex flex-grow-1 flex-column">
        <v-row class="flex-grow-0" dense>
            <v-col cols="12">
                <v-card>
                    <v-card-title>
                        Kitchen List
                        <v-btn
                            v-if="userCan('kitchens-create')"
                            class="mx-2"
                            dark
                            outlined
                            color="primary"
                            :to="{ name: 'kitchen-manage', query:{ action:'add', id:0 }}"
                        >
                            <v-icon dark>
                                mdi-plus
                            </v-icon>
                            Add Kitchen
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-text-field
                            v-model="search"
                            append-icon="mdi-magnify"
                            label="Search"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-card-title>
                    <v-data-table
                        :headers="headers"
                        :items="dataList === ''? []:dataList"
                        :search="search"
                        :loading="dataList === ''"
                        loading-text="Loading... Please wait"
                    >
                        <template #item.is_blocked="{ item }">
                            <v-icon v-if="parseInt(item.is_blocked) === 1" class="red--text">mdi-close</v-icon>
                            <v-icon  v-else>mdi-check</v-icon>
                        </template>
                        <template #item.actions="{ item }">
                            <v-btn
                                v-if="userCan('kitchens-read')"
                                icon class="text-center"
                                :to="{ name: 'kitchen-manage', query:{ action:'edit', id: item.id }}"
                            >
                                <v-icon>mdi-open-in-new</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>

    import { mapState, mapActions } from "vuex";

    export default {
        name: "KitchenList",
        props: {
            name: {
                type: String,
                default: ''
            }
        },
        components: {

        },
        data() {
            return {
                search: '',
                headers: [
                    {
                        text: 'ID',
                        align: 'start',
                        value: 'id'
                    },
                    { text: 'Name', value: 'name' },
                    { text: 'Number', value: 'number' },
                    { text: 'Shop', value: 'shop.name' },
                    { text: 'Active', value: 'is_blocked'},
                    {
                        text: 'Actions',
                        align: 'end',
                        value: 'actions',
                        sortable: false
                    }
                ],
                dataList: '',
            }
        },

        mounted() {
            this.fetchList();
        },

        beforeDestroy() {
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            fetchList: function () {
                const _this = this;
                axios.get('/web/v1/kitchen')
                    .then(function (response) {
                        console.log(response);
                        _this.dataList = response.data.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                        this.showError({message:'Something Went Wrong!', error: error})
                    });
            }
        }
    }
</script>
