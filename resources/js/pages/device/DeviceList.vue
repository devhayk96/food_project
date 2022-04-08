<template>
    <div class="d-flex flex-grow-1 flex-column">
        <v-row class="flex-grow-0" dense>
            <v-col cols="12">
                <v-card>
                    <v-card-title>
                        Devices
                        <v-btn
                            v-if="userCan('device-create')"
                            class="mx-2"
                            dark
                            outlined
                            color="primary"
                            :to="{ name: 'device-manage', query: { action: 'add', id: 0 }}"
                        >
                            <v-icon dark>
                                mdi-plus
                            </v-icon>
                            Add Device
                        </v-btn>
                    </v-card-title>

                    <v-data-table
                        :headers="headers"
                        :items="$store.state.device.listData === ''? [] : $store.state.device.listData"
                        :search="search"
                        :loading="$store.state.device.listData === ''"
                        loading-text="Loading... Please wait"
                        hide-default-footer
                        class="elevation-1"
                        disable-filtering
                        disable-pagination
                        disable-sort
                    >
                        <template #item.shop="{ item }">
                            <div>{{ item.shop.name }}</div>
                        </template>
                        <template #item.actions="{ item }">
                            <v-menu
                                left
                                offset-y
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <img
                                        src="/images/custom/three-dots.svg"
                                        alt="three-dots"
                                        width="20"
                                        class="mr-2 dots-img"
                                        v-on="on"
                                    />
                                </template>
                                <v-list class="actions-list actions-menu_wrap">
                                    <v-list-item class="actions-list_item">
                                        <template>
                                            <v-btn
                                                v-if="userCan('device-update')"
                                                icon
                                                :to="{name: 'device-manage', query: {action: 'edit', id: item.id}}"
                                            >
                                                <v-icon class="actions-list_icon">mdi-pencil</v-icon>
                                                <span class="actions-list_text">Edit</span>
                                            </v-btn>
                                        </template>
                                    </v-list-item>
                                    <v-list-item class="actions-list_item">
                                        <template>
                                            <v-btn
                                                v-if="userCan('device-delete')"
                                                icon
                                                @click="deleteDeviceId = item.id; deleteDialog = true"
                                                v-bind="item"
                                            >
                                                <v-icon class="actions-list_icon">mdi-delete-outline</v-icon>
                                                <span class="actions-list_text">Delete</span>
                                            </v-btn>
                                        </template>
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>

        <v-dialog
            v-model="deleteDialog"
            persistent
            max-width="400"
        >
            <v-card>
                <v-card-text class="text-h5">{{ deleteDialogTitle }}</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="primary darken-1"
                        text
                        @click="deleteDialog = false; deleteOptionGroupId = null"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="red darken-1"
                        text
                        @click="deleteDevice()"
                    >
                        Delete
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    import {mapActions} from "vuex";

    export default {
        components: {

        },
        data() {
            return {
                page: 1,
                pageCount: 0,
                itemsPerPage: 10,
                date: null,
                name: null,
                search: '',
                sortBy: 'created_at',
                sortByType: 'desc',
                sortList:[
                    { text:'Date', value:'created_at' },
                    { text:'Name', value:'name' },
                ],
                headers: [
                    { text: 'Name', value: 'name' },
                    { text: 'Code', value: 'code' },
                    { text: 'Shop', value: 'shop' },
                    { text: 'Orders Auto Refresh Time', value: 'orders_auto_refresh_time' },
                    { text: 'Finished Orders Delay Time', value: 'finished_orders_delay_time' },
                    { text: 'Actions', align: 'end', value: 'actions', sortable: false }
                ],
                deleteDialog: false,
                deleteDialogTitle: 'Are you sure you want to delete this device?',
                deleteDeviceId: null,
            }
        },

        mounted() {
            this.fetchList();
        },

        beforeDestroy() {
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            ...mapActions('device', ['fetchList']),
            deleteDevice() {
                axios.delete(`/web/v1/device/${this.deleteDeviceId}`)
                    .then( response => {
                        if (response.data.message) {
                            let deviceIndex = this.$store.state.device.listData.findIndex( (item, index) => {
                                if (item.id === this.deleteDeviceId) {
                                    return true;
                                }
                            });
                            this.$store.state.device.listData.splice(deviceIndex, 1);

                            this.showSuccess(response.data.message);
                        } else {
                            this.showError({ message: "Error : ", error: { message: "Something Went Wrong." } });
                        }

                        this.deleteDialog = false;
                        this.deletedeviceId = null;
                    })
                    .catch( error => {
                        this.deleteDialog = false;
                        this.deleteDeviceId = null;
                        console.log(error);
                        this.showError({ message:'Error: ', error: error });
                    });
            },
        }
    }
</script>



<style lang="scss">

.theme--light.v-data-table > .v-data-table__wrapper > table > thead > tr > th {
    color: #757575;
}

.v-data-table table tbody tr {
    height: 100px;
}

.theme--light.v-data-table >
.v-data-table__wrapper >
table > tbody > tr:not(:last-child) >
td:not(.v-data-table__mobile-row),
.theme--light.v-data-table >
.v-data-table__wrapper >
table > tbody > tr:not(:last-child) >
th:not(.v-data-table__mobile-row) {
    border-bottom: 1px solid #eee!important;
}
.v-menu__content {
    border-radius: 0;
}
.actions-list_item {
    .v-btn--icon.v-size--default {
        width: auto;
    }
    .v-btn:before {
        background-color: unset;
    }
}
.actions-menu_wrap {
    position: relative;
}


.dots-img {
    cursor: pointer;
}
.actions-list {
    border: 1px solid #eee;
    width: 120px;
    border-radius: 0;
    padding: 0;
    font-size: 12px!important;

    &_item {
        border-bottom: 1px solid #eee;
        padding-left: 5px;

        &:hover {
            background-color: #eeeeee;
        }
    }

    &_text {
        font-size: 12px;
    }

    &_icon {
        &:before {
            font-size: 14px!important;
        }
    }
}

</style>
