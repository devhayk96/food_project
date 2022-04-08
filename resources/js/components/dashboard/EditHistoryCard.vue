<template>
    <v-card-text class="query-info">
        <v-col cols="12">
            <v-row>
                <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="$store.state.infoBox.createdBy"
                        label="Create By"
                        readonly
                        disabled
                    ></v-text-field>
                </v-col>

                <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="$store.state.infoBox.createdAt"
                        label="Creation Date"
                        readonly
                        disabled
                        type="datetime"
                    ></v-text-field>
                </v-col>

                <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="$store.state.infoBox.lastUpdateBy"
                        label="Last Edit By"
                        readonly
                        disabled
                    ></v-text-field>
                </v-col>

                <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="$store.state.infoBox.lastUpdateAt"
                        label="Last Edit Date"
                        readonly
                        disabled
                        type="datetime"
                    ></v-text-field>
                </v-col>

            </v-row>
        </v-col>
    </v-card-text>
</template>
