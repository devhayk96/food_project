<template>
    <v-card>
        <v-card-title>
            Order Sources
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
            :items="sources"
            :search="search"
        >
            <template v-slot:item.is_active="{ item }">
                <v-icon
                    v-if="item.is_active === 0"
                    class="red--text"
                >
                    mdi-close
                </v-icon>
                <v-icon
                    v-if="item.is_active === 1"
                >
                    mdi-check
                </v-icon>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>

export default {
    data () {
        return {
            search: '',
            headers: [
                {
                    text: 'Poshub ID',
                    align: 'start',
                    value: 'id'
                },
                { text: 'Code', value: 'code' },
                { text: 'Name', value: 'name' },
                { text: 'Source type', value: 'order_source_type.name'},
                { text: 'Active', value: 'is_active'}
            ],
            sources: []
        }
    },

    created () {
        this.initialize()
    },

    methods: {
        initialize() {
            let self = this;
            axios.get('/web/v1/order-sources').then(function (response) {
                console.log(response.data.data);
                self.sources = response.data.data;
            })
        },
    }
}
</script>
