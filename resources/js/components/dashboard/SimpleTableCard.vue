<template>
    <v-card height="100%" :elevation="elevationCard">
        <v-data-table
            :headers="generateHeaders"
            :items="items"
            :items-per-page="-1"
            :class="elevation"
            disable-pagination
            hide-default-footer
        >
            <template v-slot:top v-show="label">
                <v-row class="table-title mt-0">
                    <v-col class="col-12 col-lg-7 py-0"><h2><v-icon></v-icon>{{ label }}</h2></v-col>
                    <v-col class="col-12 col-lg-5 py-0 d-flex justify-end">
                        <p class="pr-5">Shop Name: <span>{{ shopname }}</span></p>
                        <p>{{ sourcename }}: <span>{{ orderid }}</span></p>
                    </v-col>
                </v-row>
            </template>

            <template v-for="(value, key) in merge" v-slot:[`item.${key}`]="{ item }">
                <div class="media">
                    <img :src="item[value]" class="rounded-circle">
                    <div class="media-body d-flex flex-column">
                        <span>{{ item[key] }}</span>
                        <span v-for="value in subtext" class="fs-12">
                            - {{ item[value] }}
                        </span>
                    </div>
                </div>
            </template>

            <template v-slot:body.append>
                <tr>
                    <td colspan="5">
                        <span>Subtotal:</span>
                        <span class="font-weight-bold">{{ subtotal }}</span>
                    </td>
                </tr>
            </template>

            <template v-slot:no-data>No Data</template>
        </v-data-table>
    </v-card>
</template>

<script>
export default {
    props: {
        data: {
            type: Array,
            default: null
        },
        label: {
            type: String,
            default: ''
        },
        shopname: {
            type: String,
            default: ''
        },
        subtotal: {
            type: String,
            default: ''
        },
        sourcename: {
            type: String,
            default: ''
        },
        orderid: {
            type: String,
            default: ''
        },
        language: {
            type: String,
            default: ''
        },
        exclude: {
            type: Array,
            default: null,
        },
        elevation: {
            type: String,
            default: 'elevation-1'
        },
        elevationCard: {
            type: String,
            default: '0'
        },
        merge: {
            type: Object,
            default: null
        },
        subtext: {
            type: Object,
            default: null
        },
        headersProps: {
            type: Array,
            default: null
        }
    },

    data() {
        return {
            headersTmp: [],
            items: this.data
        }
    },

    methods: {
        getHeaders() {
            return this.headersProps ? this.headersProps : this.generateHeaders();
        },

        generateHeaders() {
            let headersTmp = [];
            for (let item of this.data) {
                headersTmp = [...new Set([...headersTmp, ...Object.keys(item)])];
            }

            // remove column using props exclude
            if (!!this.exclude) {
                headersTmp = headersTmp.filter((el) => !this.exclude.includes(el));
            }
            return headersTmp;
        }
    },

    computed: {
        generateHeaders: {
            set: function() {},
            get: function() {
                this.headersTmp = this.getHeaders();
                return this.headersTmp.map(item => {
                    const container = {};
                    container.text = this.$te(this.language + '.' + item.text) ? this.$t(this.language + '.' + item.text) : item.text.replace(/_/g, " ");
                    container.value = item.value;
                    container.align = item.align;
                    return container;
                });
            }
        }
    }
}
</script>

<style>
    .fs-12 {
        font-size:12px;
    }
</style>
