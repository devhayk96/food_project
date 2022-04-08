<template>
    <v-card>
        <v-card-title>
            Addresses
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
            :items="addresses"
            :search="search"
        ></v-data-table>
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
                { text: 'Street', value: 'street' },
                { text: 'Postcode', value: 'postcode' },
                { text: 'City', value: 'city'},
                { text: 'Extra', value: 'extra'}
            ],
            addresses: []
        }
    },

    created () {
        this.initialize()
    },

    methods: {
        initialize() {
            let self = this;
            axios.get('/web/v1/addresses').then(function (response) {
                console.log(response.data.data);
                self.addresses = response.data.data;
            })
        },
    }
}
</script>
