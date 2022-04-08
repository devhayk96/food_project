<template>
    <div class="d-flex flex-grow-1 flex-column">
        <v-row class="flex-grow-0" dense>
            <div class="d-flex w-full">
                <order-source-types-card
                    class="h-full types-card--block"
                    :loading="isLoading1"
                    :refresh="refreshCardData"
                    @refreshed="refreshed"
                    @editEvent="editSourceType"
                    @createEvent="newSourceTypePanel"
                ></order-source-types-card>
                <right-side-card
                :showcard="show"
                :title="RightCardTitle"
                :refresh="refreshRightCard"
                :resource="RightSideCardData">
                <template v-slot:cardcontent>
                    <create-edit
                    @refreshData="refreshSourceType"
                    :cardProgress="RightSideCardData"
                    />
                </template>
                </right-side-card>
            </div>
        </v-row>
        <v-snackbar v-model="snackbar.show" :timeout="snackbar.timeout" :color="snackbar.color" bottom>
            {{ snackbar.text }}
        <v-btn v-if="snackbar.timeout === 0" color="white" text @click="snackbar.show = false">{{
                $t('common.close')
            }}
        </v-btn>
        </v-snackbar>
    </div>
</template>

<script>

import CreateEdit from '../../../components/dashboard/OrderSourceTypes/CreateEdit'
import RightSideCard from '../../../components/dashboard/operations/RightSideCard';
import OrderSourceTypesCard from '../../../components/dashboard/operations/OrderSourceTypesCard';

export default {
    components: {
        CreateEdit,
        RightSideCard,
        OrderSourceTypesCard
    },
    data() {
        return {
            loadingInterval: null,
            isLoading1: true,
            RightSideCardData:{
                event:'',
            },
            show: false,
            tableDataLength:0,
            refreshCardData:0,
            RightCardTitle: '',
            refreshRightCard: 0,
            snackbar:{
                text:'',
                color:'',
                show: false,
                timeout:3000,
            }
        }
    },

    mounted() {
        let count = 0

        // DEMO delay for loading graphics
        this.loadingInterval = setInterval(() => {
            this[`isLoading${count++}`] = false
            if (count === 4) this.clear()
        }, 400)
    },

    beforeDestroy() {
        this.clear()
    },

    methods: {
        clear() {
            clearInterval(this.loadingInterval)
        },

        newSourceTypePanel(value) {
            this.show = Math.random();
            this.tableDataLength = value.eventsCountNow + 1;
            this.RightCardTitle = `Create Source Type #${ this.tableDataLength }`
            this.RightSideCardData = {
                data: value,
            };
        },

        editSourceType(value) {
            this.show = Math.random();
            this.RightCardTitle = 'Edit Source Type',
            this.RightSideCardData = {
                data: value,
            };
        },

        refreshed(value) {
            if( value.progress !== 'edit' ) {
                this.RightCardTitle = `Create Source Type #${ value.lengthNow + 1 }`
            }
            if( value.progress === 'delete') {
                this.callSnackBar('success', 'Source type is successfully deleted');
            }else if( value.progress === 'copy' ) {
                this.callSnackBar('success', 'Source type is successfully copied');
            }
            this.refreshRightCard = Math.random();
        },

        refreshSourceType(value) {
            if( value.e === 'create' ) {
                this.callSnackBar('success', 'Source type has been successfully created');
                this.refreshCardData = {data:Math.random(), e:value.e};
            }else if(value.e === 'edit') {
                this.callSnackBar('success', 'Source type has been successfully edited');
                this.refreshCardData = {data:Math.random(), e:value.e};
            }
        },

        callSnackBar(color, text) {
            this.snackbar.color = color;
            this.snackbar.text = text;
            this.snackbar.show = true;
        }

    }
}
</script>

<style scoped>
    .types-card--block {
        width:100%;
        min-height:380px;
        padding-right:25px;
    }
</style>
