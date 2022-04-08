<template>
    <div class="d-flex flex-grow-1 flex-column" v-if="shop || shopFromTable">
        <v-row class="flex-grow-0" dense>
            <v-col cols="12">
                <v-card>
                    <v-card-title>
                        {{ $t('shop.shop_details') }} - {{ getShop.name }}
                    </v-card-title>
                    <v-card-text>
                        <v-tabs v-model="tabs" grow>
                            <v-tabs-slider></v-tabs-slider>
                            <v-tab href="#home"><v-icon class="mr-3">mdi-home</v-icon>{{ $t('shop.home') }}</v-tab>
                            <v-tab href="#opening-hours"><v-icon class="mr-3">mdi-clock-time-eight</v-icon>{{ $t('shop.opening_hours') }}</v-tab>
                            <v-tab href="#delivery-hours"><v-icon class="mr-3">mdi-clock-time-four</v-icon>{{ $t('shop.delivery_hours') }}</v-tab>
                            <v-tab href="#api"><v-icon class="mr-3">mdi-cloud-tags</v-icon>API</v-tab>
                        </v-tabs>

                        <v-tabs-items v-model="tabs">
                            <v-tab-item :value="'home'">
                                <v-card flat class="mt-7">
                                    <v-card-text>
                                        <v-form ref="homeForm">
                                            <v-row>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.name') | capitalize"
                                                        name="name"
                                                        prepend-icon="mdi-account"
                                                        type="text"
                                                        v-model="getShop.name"
                                                        :rules="textRules"
                                                        required
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.address') | capitalize"
                                                        name="address"
                                                        prepend-icon="mdi-map-marker"
                                                        type="text"
                                                        v-model="getShop.address.street"
                                                        :rules="textRules"
                                                        required
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.address') + ' 2' | capitalize"
                                                        name="address2"
                                                        prepend-icon="mdi-map-marker"
                                                        type="text"
                                                        v-model="getShop.address.street_extra"
                                                    ></v-text-field>
                                                </v-col>
                                            </v-row>

                                            <v-row>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.post_code') | capitalize"
                                                        name="postalcode"
                                                        prepend-icon="mdi-map-marker"
                                                        type="text"
                                                        v-model="getShop.address.postcode"
                                                        :rules="textRules"
                                                        required
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.city') | capitalize"
                                                        name="city"
                                                        prepend-icon="mdi-map-marker"
                                                        type="text"
                                                        v-model="getShop.address.city"
                                                        :rules="textRules"
                                                        required
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.country') | capitalize"
                                                        name="country"
                                                        prepend-icon="mdi-map-marker"
                                                        type="text"
                                                        v-model="getShop.address.country"
                                                        :rules="textRules"
                                                        required
                                                    ></v-text-field>
                                                </v-col>
                                            </v-row>

                                            <v-row>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.telephone') | capitalize"
                                                        name="telephone"
                                                        prepend-icon="mdi-phone"
                                                        type="text"
                                                        v-model="getShop.phone"
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.email') | capitalize"
                                                        name="email"
                                                        prepend-icon="mdi-email"
                                                        type="text"
                                                        v-model="getShop.email"
                                                        :rules="emailRules"
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.chamber_commerce') | capitalize"
                                                        name="chamber"
                                                        prepend-icon="mdi-receipt"
                                                        type="text"
                                                        v-model="getShop.company_number"
                                                    ></v-text-field>
                                                </v-col>
                                            </v-row>

                                            <v-row>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.vat') | capitalize"
                                                        name="vat"
                                                        prepend-icon="mdi-receipt"
                                                        type="text"
                                                        v-model="getShop.vat"
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.iban')"
                                                        name="iban"
                                                        prepend-icon="mdi-bank"
                                                        type="text"
                                                        v-model="getShop.iban"
                                                        :rules="ibanRules"
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="2" cols="6" class="d-flex justify-center">
                                                    <v-switch
                                                        dense
                                                        inset
                                                        v-model="getShop.is_active"
                                                        :label="getShop.is_active
                                                        ? $options.filters.capitalize($t('shop.active'))
                                                        : $options.filters.capitalize($t('shop.inactive'))"
                                                    ></v-switch>
                                                </v-col>
                                                <v-col md="2" cols="6" class="d-flex justify-center">
                                                    <v-switch
                                                        dense
                                                        inset
                                                        v-model="getShop.is_visible"
                                                        :label="getShop.is_visible
                                                        ? $options.filters.capitalize($t('shop.visible'))
                                                            :$options.filters.capitalize($t('shop.hidden'))"
                                                    ></v-switch>
                                                </v-col>
                                            </v-row>

                                        </v-form>
                                    </v-card-text>

                                    <v-card-actions v-if="userCan('shops-update')">
                                        <v-spacer></v-spacer>
                                        <v-btn :loading="loadingSave"
                                               :disabled="loadingSave"
                                               depressed
                                               color="primary"
                                               @click="saveHomeForm">
                                            {{ $t('common.save') | uppercase }}
                                        </v-btn>
                                    </v-card-actions>

                                </v-card>
                            </v-tab-item>

                            <v-tab-item :value="'opening-hours'">
                                <v-card flat class="mt-7">
                                    <v-card-text>
                                        <v-form ref="hoursForm">
                                            <template v-for="(data, day) in days">
                                                <v-row>
                                                    <v-col cols="6" md="2">
                                                        <h3 class="pt-1 text-no-wrap">{{ $t('shop.' + day.toLowerCase()) | capitalize }}</h3>
                                                    </v-col>
                                                    <v-col cols="6" md="2" class="text-left">
                                                        <v-switch
                                                            dense
                                                            inset
                                                            v-model="days[day].isOpen"
                                                            :label="days[day].isOpen
                                                            ? $options.filters.capitalize($t('shop.open'))
                                                            : $options.filters.capitalize($t('shop.close'))"
                                                            class="mt-1 pt-0"
                                                            @change="()=>{days[day].isDelivery=days[day].isOpen?days[day].isDelivery:days[day].isOpen}"
                                                        ></v-switch>
                                                    </v-col>
                                                    <v-col cols="12" md="8">
                                                        <v-row v-for="(slot, index) in data.timeSlots" :key="day + index">
                                                            <v-col cols="12" md="4">
                                                                <v-text-field
                                                                    v-model="slot.from"
                                                                    @change="changeHoursRules"
                                                                    :disabled="!data.isOpen"
                                                                    :label="$t('shop.select_time')"
                                                                    prepend-icon="mdi-clock-time-four-outline"
                                                                    dense
                                                                    :rules="hoursRules(data.isOpen)"
                                                                    type="time"
                                                                    required
                                                                ></v-text-field>
                                                            </v-col>
                                                            <v-col cols="12" md="1" class="text-center">
                                                                <h4 class="pt-1 text-no-wrap">{{ $t('shop.until') | capitalize }}</h4>
                                                            </v-col>
                                                            <v-col cols="12" md="4">
                                                                <v-text-field
                                                                    v-model="slot.to"
                                                                    @change="changeHoursRules"
                                                                    :disabled="!data.isOpen"
                                                                    :label="$t('shop.select_time')"
                                                                    prepend-icon="mdi-clock-time-four-outline"
                                                                    dense
                                                                    :rules="hoursRules(data.isOpen)"
                                                                    type="time"
                                                                    required
                                                                ></v-text-field>
                                                            </v-col>
                                                            <v-col cols="12" md="3" class="text-right">
                                                                <v-btn :disabled="!data.isOpen"
                                                                       fab dark depressed x-small color="primary" @click="initSetTime(day, index)">
                                                                    <v-icon dark>mdi-clock-time-four-outline</v-icon>
                                                                </v-btn>
                                                                <v-btn :disabled="data.timeSlots.length === 1 || !data.isOpen"
                                                                       fab dark depressed x-small color="red" @click="deleteTimeSlot(day, index)">
                                                                    <v-icon dark>mdi-delete</v-icon>
                                                                </v-btn>
                                                                <v-btn :disabled="!slot.from || !slot.to || !data.isOpen"
                                                                       fab dark depressed x-small color="green" @click="addTimeSlot(day, index)">
                                                                    <v-icon dark>mdi-plus</v-icon>
                                                                </v-btn>
                                                            </v-col>
                                                        </v-row>
                                                    </v-col>
                                                </v-row>
                                                <v-divider class="mb-3 mt-1"></v-divider>
                                            </template>
                                        </v-form>
                                    </v-card-text>

                                    <v-card-actions v-if="userCan('shops-update')">
                                        <v-spacer></v-spacer>
                                        <v-btn :loading="loadingSave"
                                               :disabled="loadingSave"
                                               depressed
                                               color="primary"
                                               @click="saveHoursForm">
                                            {{ $t('common.save') | uppercase }}
                                        </v-btn>
                                    </v-card-actions>

                                </v-card>
                            </v-tab-item>

                            <v-tab-item :value="'delivery-hours'">
                                <v-card flat class="mt-7">
                                    <v-card-text>
                                        <v-form ref="deliveryForm">
                                            <template v-for="(data, day) in days">
                                                <v-row>
                                                    <v-col cols="12" md="2">
                                                        <h3 class="pt-1 text-no-wrap">{{ $t('shop.' + day.toLowerCase()) | capitalize }}</h3>
                                                    </v-col>
                                                    <v-col cols="6" md="2" class="text-left">
                                                        <v-switch
                                                            dense
                                                            inset
                                                            v-model="days[day].isDelivery"
                                                            :label="days[day].isDelivery
                                                            ? $options.filters.capitalize($t('shop.open'))
                                                            : $options.filters.capitalize($t('shop.close'))"
                                                            class="mt-1 pt-0"
                                                            :disabled="!days[day].isOpen"
                                                        ></v-switch>
                                                    </v-col>
                                                    <v-col cols="12" md="8">
                                                        <v-row v-for="(slot, index) in data.delivery" :key="'delivery-' + day + index">
                                                            <v-col cols="12" md="4">
                                                                <v-text-field
                                                                    v-model="slot.from"
                                                                    @change="changeDeliveryHoursRules"
                                                                    :disabled="!data.isDelivery"
                                                                    :label="$t('shop.select_time')"
                                                                    prepend-icon="mdi-clock-time-four-outline"
                                                                    dense
                                                                    :rules="hoursRules(data.isDelivery)"
                                                                    type="time"
                                                                    required
                                                                ></v-text-field>
                                                            </v-col>
                                                            <v-col cols="12" md="1" class="text-center">
                                                                <h4 class="pt-1 text-no-wrap">{{ $t('shop.until') | capitalize }}</h4>
                                                            </v-col>
                                                            <v-col cols="12" md="4">
                                                                <v-text-field
                                                                    v-model="slot.to"
                                                                    @change="changeDeliveryHoursRules"
                                                                    :disabled="!data.isDelivery"
                                                                    :label="$t('shop.select_time')"
                                                                    prepend-icon="mdi-clock-time-four-outline"
                                                                    dense
                                                                    :rules="hoursRules(data.isDelivery)"
                                                                    type="time"
                                                                    required
                                                                ></v-text-field>
                                                            </v-col>
                                                            <v-col cols="12" md="3" class="text-right">
                                                                <v-btn :disabled="!data.isDelivery"
                                                                       fab dark depressed x-small color="primary" @click="initDelivery(day, index)">
                                                                    <v-icon dark>mdi-clock-time-four-outline</v-icon>
                                                                </v-btn>
                                                                <v-btn :disabled="data.delivery.length === 1 || !data.isDelivery"
                                                                       fab dark depressed x-small color="red" @click="deleteDelivery(day, index)">
                                                                    <v-icon dark>mdi-delete</v-icon>
                                                                </v-btn>
                                                                <v-btn :disabled="!slot.from || !slot.to || !data.isDelivery"
                                                                       fab dark depressed x-small color="green" @click="addDelivery(day, index)">
                                                                    <v-icon dark>mdi-plus</v-icon>
                                                                </v-btn>
                                                            </v-col>
                                                        </v-row>
                                                    </v-col>
                                                </v-row>
                                                <v-divider class="mb-3 mt-1"></v-divider>
                                            </template>
                                        </v-form>
                                    </v-card-text>

                                    <v-card-actions v-if="userCan('shops-update')">
                                        <v-spacer></v-spacer>
                                        <v-btn :loading="loadingSave"
                                               :disabled="loadingSave"
                                               depressed
                                               color="primary"
                                               @click="saveDeliveryForm">
                                            {{ $t('common.save') | uppercase }}
                                        </v-btn>
                                    </v-card-actions>

                                </v-card>
                            </v-tab-item>

                            <v-tab-item :value="'api'">
                                <v-card flat class="mt-7">
                                    <v-card-text>
                                        <v-row>
                                            <v-col cols="12" lg="4" v-for="(source, index) in getShop.order_sources" :key="source.id + '-' + index">
                                                <v-form :ref="'apiForm' + source.code.replace(/[^0-9a-zA-Z]/g,'')"
                                                        :disabled="!parseInt(source.order_source_type.is_active)"
                                                        style="height: 100%"
                                                >
                                                    <v-card outlined height="100%">
                                                        <v-card-title>
                                                            {{ source.order_source_type.name | capitalize }} API
                                                            <v-spacer></v-spacer>
                                                            <v-switch inset color="success" v-model="source.is_active"
                                                                      v-if="source.order_source_type.code !== 'pos'"
                                                            ></v-switch>
                                                        </v-card-title>
                                                        <v-card-text class="pc-24-22">

                                                            <v-list-item class="grow" v-if="source.order_source_type.code !== 'pos'">
                                                                <v-row align="center" justify="start">
                                                                    {{ $t('shop.auto_accepted_order') | uppercase }}:
                                                                </v-row>
                                                                <v-row align="center" justify="end">
                                                                    <v-switch inset dense color="info"
                                                                              :disabled="!source.is_active"
                                                                              v-model="source.is_auto_accept">
                                                                    </v-switch>
                                                                </v-row>
                                                            </v-list-item>

                                                            <v-divider class="mb-3 mt-1"></v-divider>
                                                            <v-row v-if="source.credentials && source.order_source_type.code === 'thuisbezorgd'">
                                                                <v-col cols="12">
                                                                    <v-text-field
                                                                        :label="'Thuisbezorgd ' + $t('shop.restaurant_id')"
                                                                        name="thuisbezorgd_restaurant_id"
                                                                        prepend-icon="mdi-bank"
                                                                        type="text"
                                                                        v-model="source.credentials.restaurantId"
                                                                        :rules="fnTextRules(source.is_active)"
                                                                        required
                                                                        :disabled="!source.is_active"
                                                                    ></v-text-field>

                                                                    <v-text-field
                                                                        :label="$t('shop.username') | capitalize"
                                                                        name="username"
                                                                        prepend-icon="mdi-account"
                                                                        type="text"
                                                                        v-model="source.credentials.username"
                                                                        :rules="fnTextRules(source.is_active)"
                                                                        required
                                                                        :disabled="!source.is_active"
                                                                    ></v-text-field>

                                                                    <v-text-field
                                                                        :label="$t('shop.password') | capitalize"
                                                                        name="password"
                                                                        prepend-icon="mdi-lock"
                                                                        v-model="source.credentials.password"
                                                                        :rules="fnTextRules(source.is_active)"
                                                                        required
                                                                        :disabled="!source.is_active"
                                                                        :append-icon="showPassword[index] ? 'mdi-eye' : 'mdi-eye-off'"
                                                                        :type="showPassword[index] ? 'text' : 'password'"
                                                                        @click:append="$set(showPassword, index, !showPassword[index])"
                                                                    ></v-text-field>

                                                                    <v-text-field
                                                                        :label="$t('shop.api_key') | capitalize"
                                                                        name="apiKey"
                                                                        prepend-icon="mdi-key"
                                                                        type="text"
                                                                        v-model="source.credentials.apiKey"
                                                                        :rules="fnTextRules(source.is_active)"
                                                                        required
                                                                        :disabled="!source.is_active"
                                                                    ></v-text-field>
                                                                </v-col>
                                                            </v-row>

                                                            <v-row v-if="source.credentials && source.order_source_type.code === 'ubereats'">
                                                                <v-col cols="12">
                                                                    <v-text-field
                                                                        :label="$t('shop.restaurant_id') | capitalize"
                                                                        name="restaurant_id"
                                                                        prepend-icon="mdi-bank"
                                                                        type="text"
                                                                        v-model="source.credentials.restaurantId"
                                                                        :rules="fnTextRules(source.is_active)"
                                                                        required
                                                                        :disabled="!source.is_active"
                                                                    ></v-text-field>
                                                                    <v-text-field
                                                                        :label="$t('shop.client_id') | capitalize"
                                                                        name="client_id"
                                                                        prepend-icon="mdi-account"
                                                                        type="text"
                                                                        v-model="source.credentials.clientId"
                                                                        :rules="fnTextRules(source.is_active)"
                                                                        required
                                                                        :disabled="!source.is_active"
                                                                    ></v-text-field>

                                                                    <v-text-field
                                                                        :label="$t('shop.client_secret') | capitalize"
                                                                        name="client_secret"
                                                                        prepend-icon="mdi-lock"
                                                                        v-model="source.credentials.clientSecret"
                                                                        :rules="fnTextRules(source.is_active)"
                                                                        required
                                                                        :disabled="!source.is_active"
                                                                        :append-icon="showPassword[index] ? 'mdi-eye' : 'mdi-eye-off'"
                                                                        :type="showPassword[index] ? 'text' : 'password'"
                                                                        @click:append="$set(showPassword, index, !showPassword[index])"
                                                                    ></v-text-field>
                                                                </v-col>

                                                                <!--<v-col cols="12">
                                                                    <v-dialog
                                                                        v-model="dialogUberEatsKey"
                                                                        scrollable
                                                                        min-width="300px"
                                                                        max-width="95%"
                                                                    >
                                                                        <template v-slot:activator="{ on, attrs }">
                                                                            <v-btn
                                                                                color="primary"
                                                                                dark
                                                                                v-bind="attrs"
                                                                                v-on="on"
                                                                                width="100%"
                                                                                :disabled="!source.is_active"
                                                                            >
                                                                                Uber Eats {{ $t('shop.keyring') }}
                                                                            </v-btn>
                                                                        </template>
                                                                        <v-card>
                                                                            <v-card-title>Uber Eats {{ $t('shop.keyring') | capitalize }}</v-card-title>
                                                                            <v-divider></v-divider>

                                                                            <v-card-text style="height: 400px;">
                                                                                <v-data-table
                                                                                    :headers="uberEatsHeaders"
                                                                                    :items="getItems(source.credentials.wallet)"
                                                                                    :items-per-page="-1"
                                                                                    disable-pagination
                                                                                    hide-default-footer
                                                                                    id="uber-eats-table"
                                                                                >
                                                                                    <template v-slot:item.actions="{ item }">
                                                                                        <v-layout justify-center>
                                                                                            <v-checkbox
                                                                                                dense
                                                                                                v-if="item.tokenType"
                                                                                                v-model="item.autoRenew"
                                                                                                :label="$t('shop.auto_renew') | capitalize"
                                                                                                color="info"
                                                                                                hide-details
                                                                                                class="mt-0 mr-3"
                                                                                                @click="showConfirmModal()"
                                                                                            >
                                                                                                ></v-checkbox>

                                                                                            <v-tooltip top>
                                                                                                <template v-slot:activator="{ on, attrs }">
                                                                                                    <v-btn v-if="!item.tokenType"
                                                                                                           color="success"
                                                                                                           v-bind="attrs"
                                                                                                           v-on="on"
                                                                                                           class="mx-1"
                                                                                                           @click="showConfirmModal()"
                                                                                                    >
                                                                                                        <v-icon right dark>mdi-key-plus</v-icon>
                                                                                                    </v-btn>
                                                                                                </template>
                                                                                                <span>{{ $t('common.create') | capitalize }}</span>
                                                                                            </v-tooltip>

                                                                                            <v-tooltip top>
                                                                                                <template v-slot:activator="{ on, attrs }">
                                                                                                    <v-btn v-if="item.tokenType"
                                                                                                           color="error"
                                                                                                           v-bind="attrs"
                                                                                                           v-on="on"
                                                                                                           class="mx-1"
                                                                                                           @click="showConfirmModal()"
                                                                                                    >
                                                                                                        <v-icon right dark>mdi-key-remove</v-icon>
                                                                                                    </v-btn>
                                                                                                </template>
                                                                                                <span>{{ $t('common.delete') | capitalize }}</span>
                                                                                            </v-tooltip>

                                                                                            <v-tooltip top>
                                                                                                <template v-slot:activator="{ on, attrs }">
                                                                                                    <v-btn v-if="item.tokenType"
                                                                                                           color="primary"
                                                                                                           v-bind="attrs"
                                                                                                           v-on="on"
                                                                                                           class="mx-1"
                                                                                                           @click="showConfirmModal()"
                                                                                                    >
                                                                                                        <v-icon right dark>mdi-key-change</v-icon>
                                                                                                    </v-btn>
                                                                                                </template>
                                                                                                <span>{{ $t('common.refresh') | capitalize }}</span>
                                                                                            </v-tooltip>
                                                                                        </v-layout>
                                                                                    </template>

                                                                                </v-data-table>
                                                                            </v-card-text>

                                                                            <v-divider></v-divider>
                                                                            <v-card-actions>
                                                                                <v-spacer></v-spacer>
                                                                                <v-btn
                                                                                    color="primary"
                                                                                    dark
                                                                                    @click="dialogUberEatsKey = false"
                                                                                >
                                                                                    {{ $t('common.close') | uppercase }}
                                                                                </v-btn>
                                                                                <v-btn
                                                                                    color="primary"
                                                                                    dark
                                                                                    @click="dialogUberEatsKey = false"
                                                                                >
                                                                                    {{ $t('common.save') | uppercase }}
                                                                                </v-btn>
                                                                            </v-card-actions>
                                                                        </v-card>
                                                                    </v-dialog>
                                                                </v-col>-->
                                                            </v-row>

                                                            <v-row v-if="source.credentials && source.order_source_type.code === 'pos'">
                                                                <v-col cols="12">
                                                                    <v-text-field
                                                                        label="Token"
                                                                        name="token"
                                                                        prepend-icon="mdi-key"
                                                                        type="text"
                                                                        v-model="source.credentials.token"
                                                                        required
                                                                        readonly
                                                                    ></v-text-field>
                                                                </v-col>
                                                                <v-col v-if="userCan('shops-update')" cols="12" class="d-flex justify-space-between flex-row-reverse">
                                                                    <v-btn color="success"
                                                                           :loading="posApiLoading"
                                                                           :disabled="posApiLoading"
                                                                           @click="showConfirmModal({ action: 'createPosToken', ref: source })"
                                                                           v-if="!source.credentials.token">
                                                                        <v-icon left class="mr-3">mdi-key-plus</v-icon>
                                                                        {{ $t('common.create') }}
                                                                    </v-btn>
                                                                    <v-btn color="primary"
                                                                           :loading="posApiLoading"
                                                                           :disabled="posApiLoading"
                                                                           @click="showConfirmModal({ action: 'refreshPosToken', ref: source })"
                                                                           v-if="source.credentials.token">
                                                                        <v-icon left class="mr-3">mdi-key-change</v-icon>
                                                                        {{ $t('common.refresh') }}
                                                                    </v-btn>
                                                                    <v-btn color="error"
                                                                           :loading="posApiLoading"
                                                                           :disabled="posApiLoading"
                                                                           @click="showConfirmModal({ action: 'deletePosToken', ref: source })"
                                                                           v-if="source.credentials.token">
                                                                        <v-icon left class="mr-3">mdi-key-remove</v-icon>
                                                                        {{ $t('common.delete') }}
                                                                    </v-btn>
                                                                </v-col>
                                                            </v-row>
                                                        </v-card-text>
                                                        <v-card-actions class="action-card-api" v-if="source.order_source_type.code !== 'pos'">

                                                            <!--<v-btn
                                                                class="mr-5"
                                                                v-if="source.order_source_type.code === 'ubereats'"
                                                                :loading="syncingMenu"
                                                                :disabled="syncingMenu || !source.is_active"
                                                                depressed
                                                                min-width="100px"
                                                                color="success"
                                                                @click="()=>{syncUberEatsMenu(source.credentials.restaurantId)}">
                                                                Sync Menu
                                                            </v-btn>-->
                                                            <v-spacer></v-spacer>
                                                            <v-btn v-if="userCan('shops-update')" :loading="loadingSave"
                                                                   :disabled="loadingSave"
                                                                   depressed
                                                                   min-width="100px"
                                                                   color="primary"
                                                                   @click="saveApiForm('apiForm' + source.code.replace(/[^0-9a-zA-Z]/g,''), source)">
                                                                {{ $t('common.save') | uppercase }}
                                                            </v-btn>
                                                        </v-card-actions>
                                                    </v-card>
                                                </v-form>
                                            </v-col>
                                        </v-row>
                                    </v-card-text>

                                </v-card>
                            </v-tab-item>
                        </v-tabs-items>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-dialog
            v-model="dialogTimePicker"
            scrollable
            min-width="320px"
            width="50%"
        >
            <v-card>
                <v-card-title>{{ $t('shop.plan_your_event') | capitalize }}:</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-row
                        justify="space-around"
                        align="center"
                    >
                        <v-col style="width: 350px; flex: 0 1 auto;">
                            <h2>{{ $t('shop.start') | capitalize }}:</h2>
                            <v-time-picker
                                v-model="start"
                                format="24hr"
                                scrollable
                            ></v-time-picker>
                        </v-col>
                        <v-col style="width: 350px; flex: 0 1 auto;">
                            <h2>{{ $t('shop.end') | capitalize }}:</h2>
                            <v-time-picker
                                :disabled="!start"
                                v-model="end"
                                format="24hr"
                                scrollable
                            ></v-time-picker>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="dialogTimePicker = false">{{ $t('common.cancel') | uppercase }}</v-btn>
                    <v-btn text color="primary" @click="targetTimePicker === 'timeSlots' ? saveTimeSlotTP() : saveDeliveryTP()">{{ $t('common.ok') | uppercase }}</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog
            v-model="confirmModal"
            persistent
            max-width="290"
        >
            <v-card>
                <v-card-title class="headline">
                    {{ $t('shop.confirm_action') | uppercase }}
                </v-card-title>
                <v-card-text>{{ $t('shop.confirm_question') | uppercase }}</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        text
                        @click="confirmModal = false"
                    >
                        {{ $t('common.no') | uppercase }}
                    </v-btn>
                    <v-btn
                        color="primary"
                        text
                        @click="acceptConfirmModal"
                    >
                        {{ $t('common.yes') | uppercase }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    import iban from "iban";
    import { mapState, mapActions } from "vuex";

    export default {
        name: "ShopDetails",
        props: {
            shopFromTable: {
                type: Object,
                default: null
            }
        },
        data () {
            return {
                snackBar: false,
                snackBarText: '',
                color: "success",
                timeoutSnackBar: 4000,
                showPassword: [],
                tabs: null,
                loadingSave: null,
                dialogUberEatsKey: null,
                confirmModal: null,
                confirmModalAction: null,
                forceShop: false,
                posApiLoading: false,
                syncingMenu: false,
                days: {
                    'Monday': {
                        timeSlots: [{}],
                        delivery: [{}],
                        isOpen: false,
                        isDelivery: false
                    },
                    'Tuesday': {
                        timeSlots: [{}],
                        delivery: [{}],
                        isOpen: false,
                        isDelivery: false
                    },
                    'Wednesday': {
                        timeSlots: [{}],
                        delivery: [{}],
                        isOpen: false,
                        isDelivery: false
                    },
                    'Thursday': {
                        timeSlots: [{}],
                        delivery: [{}],
                        isOpen: false,
                        isDelivery: false
                    },
                    'Friday': {
                        timeSlots: [{}],
                        delivery: [{}],
                        isOpen: false,
                        isDelivery: false
                    },
                    'Saturday': {
                        timeSlots: [{}],
                        delivery: [{}],
                        isOpen: false,
                        isDelivery: false
                    },
                    'Sunday': {
                        timeSlots: [{}],
                        delivery: [{}],
                        isOpen: false,
                        isDelivery: false
                    }
                },

                start: null,
                end: null,

                dialogTimePicker: null,
                targetTimePicker: null,

                refSlotTime: {
                    day: null,
                    index: null
                },

                refDelivery: {
                    day: null,
                    index: null
                },

                // validation
                textRules: [v => !!v || this.$t('validations.field_required')],
                ibanRules: [
                    v => !v ? true : iban.isValid(v) || this.$t('validations.invalid_iban')
                ],
                emailRules: [
                    v => !v ? true : /.+@.+\..+/.test(v) || this.$t('validations.invalid_email'),
                ],
            }
        },

        computed: {
            ...mapState('app', ['shop']),
            uberEatsHeaders() {
                return [
                    {text: this.$t('shop.scope'), value: 'scope'},
                    {text: this.$t('shop.created_at'), value: 'createdAt'},
                    {text: this.$t('shop.expires_at'), value: 'expiresAt'},
                    {text: 'Token', value: 'accessToken'},
                    {text: this.$t('shop.actions'), sortable: false, value: 'actions', align: 'center'}
                ]
            },

            getShop() {
                return !!this.shopFromTable && !this.forceShop ? this.shopFromTable : (this.shop ? this.shop : null)
            }
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),

            changeHoursRules() {
                this.$refs.hoursForm.validate()
            },

            changeDeliveryHoursRules() {
                this.$refs.deliveryForm.validate()
            },

            hoursRules(isOpen) {
                return [
                    !isOpen ? true : v => !!v || this.$t('validations.field_required')
                ]
            },

            fnTextRules(enabled) {
                return [
                    !enabled ? true : v => !!v || 'Field is required'
                ]
            },

            getItems(wallet) {
                return Object.values(wallet);
            },

            showConfirmModal(action = null) {
                if (!!action) {
                    this.confirmModalAction = action;
                }
                this.confirmModal = true;
            },

            acceptConfirmModal() {
                this.confirmModal = false;
                if (!!this.confirmModalAction) {
                    this[this.confirmModalAction.action](this.confirmModalAction);
                }
            },

            addTimeSlot(day, index) {
                this.days[day].timeSlots.splice(index + 1, 0, { from: '', to: ''})
            },

            deleteTimeSlot(day, index) {
                if (this.days[day].timeSlots.length > 1) {
                    this.days[day].timeSlots.splice(index, 1);
                }
            },

            saveTimeSlotTP() {
                this.days[this.refSlotTime.day].timeSlots[this.refSlotTime.index].from = this.start;
                this.days[this.refSlotTime.day].timeSlots[this.refSlotTime.index].to = this.end;
                this.dialogTimePicker = !this.dialogTimePicker;
                this.start = null;
                this.end = null
            },

            saveDeliveryTP() {
                this.days[this.refDelivery.day].delivery[this.refDelivery.index].from = this.start;
                this.days[this.refDelivery.day].delivery[this.refDelivery.index].to = this.end;
                this.dialogTimePicker = !this.dialogTimePicker;
                this.start = null;
                this.end = null
            },

            addDelivery(day, index) {
                this.days[day].delivery.splice(index + 1, 0, { from: '', to: ''})
            },

            deleteDelivery(day, index) {
                if (this.days[day].delivery.length > 1) {
                    this.days[day].delivery.splice(index, 1);
                }
            },

            saveDelivery() {
                this.days[this.refDelivery.day].delivery[this.refDelivery.index].from = this.start;
                this.days[this.refDelivery.day].delivery[this.refDelivery.index].to = this.end;
                this.dialogTimePicker = !this.dialogTimePicker;
                this.start = null;
                this.end = null;
            },

            initSetTime(day, index) {
                this.refSlotTime = { day: day, index: index };
                this.dialogTimePicker = !this.dialogTimePicker;
                this.start = this.days[day].timeSlots[index].from;
                this.end = this.days[day].timeSlots[index].to;
                this.targetTimePicker = 'timeSlots'
            },

            initDelivery(day, index) {
                this.refDelivery = { day: day, index: index };
                this.dialogTimePicker = !this.dialogTimePicker;
                this.start = this.days[day].delivery[index].from;
                this.end = this.days[day].delivery[index].to;
                this.targetTimePicker = 'delivery';
            },

            saveHoursForm () {
                var self = this;
                if (this.$refs.hoursForm.validate()) {
                    this.loadingSave = true;
                    console.log('days', this.days);
                    // setTimeout(() => this.loadingSave = false, 2000);
                    var dataUpdate = {
                        shop_id : this.getShop.id,
                        type: 'opening',
                        days: this.days
                    }
                    axios.put('/web/v1/shops/time/update', dataUpdate).then(function (response) {
                        self.loadingSave = false;
                        self.showSuccess('Save successfully');
                    })
                        .catch(function (error) {
                            // console.log(error.message);
                            self.loadingSave = false;
                            self.showError({message:'Failed!', error: error});
                        });

                } else {
                    this.$nextTick(() => {
                        const el = this.$refs.hoursForm.$el.querySelector(".v-messages.error--text:first-of-type");
                        this.$vuetify.goTo(el, {offset: 75});
                    });
                }
            },

            saveDeliveryForm () {
                var self = this;
                if (this.$refs.deliveryForm.validate()) {
                    this.loadingSave = true;
                    console.log('days', this.days);
                    // setTimeout(() => this.loadingSave = false, 2000);
                    var dataUpdate = {
                        shop_id : this.getShop.id,
                        type: 'delivery',
                        days: this.days
                    }
                    axios.put('/web/v1/shops/time/update', dataUpdate).then(function (response) {
                        self.loadingSave = false;
                        self.showSuccess('Save successfully');
                    })
                        .catch(function (error) {
                            // console.log(error.message);
                            self.loadingSave = false;
                            self.showError({message:'Failed!', error: error});
                        });
                } else {
                    this.$nextTick(() => {
                        const el = this.$refs.deliveryForm.$el.querySelector('.v-messages.error--text:first-of-type');
                        this.$vuetify.goTo(el, {offset: 75});
                    });
                }
            },

            saveHomeForm () {
                if (this.$refs.homeForm.validate()) {
                    let dataUpdate = {
                        id: this.getShop.id,
                        // address_id: String(this.getShop.address.id),
                        name: this.getShop.name,
                        address_street: this.getShop.address.street,
                        address_street_extra: this.getShop.address.street_extra,
                        address_postcode: this.getShop.address.postcode,
                        address_city: this.getShop.address.city,
                        address_country: this.getShop.address.country,
                        phone: this.getShop.phone,
                        email: this.getShop.email,
                        company_number: this.getShop.company_number,
                        vat: this.getShop.vat,
                        iban: this.getShop.iban,
                        is_open: this.getShop.is_open,
                        is_delivering: this.getShop.is_delivering,
                        is_active: this.getShop.is_active,
                        is_visible: this.getShop.is_visible
                    }
                    this.loadingSave = true;
                    let self = this;
                    axios.put('/web/v1/shops/' + this.getShop.id, dataUpdate).then(function (response) {
                        self.loadingSave = false;
                        self.showSuccess('Save completed successfully');
                    })
                        .catch(function (error) {
                            // console.log(error.message);
                            self.loadingSave = false;
                            self.showError({message:'Failed!', error: error});
                        });

                } else {
                    this.$nextTick(() => {
                        const el = this.$refs.homeForm.$el.querySelector(".v-messages.error--text:first-of-type");
                        this.$vuetify.goTo(el, {offset: 75});
                    });
                }
            },

            saveApiForm (form, source) {
                let formRef = this.$refs[form][0];
                if (formRef.validate()) {
                    let dataUpdate = {
                        id: source.id,
                        is_active: source.is_active,
                        is_auto_accept: source.is_auto_accept,
                        credentials: source.credentials
                    }

                    this.loadingSave = true;
                    let self = this;
                    axios.put('/web/v1/order-sources/' + source.id, dataUpdate).then(function (response) {
                        self.loadingSave = false;
                        self.showSuccess('Save completed successfully');
                    })
                        .catch(function (error) {
                            console.log(error);
                            self.loadingSave = false;
                            self.showError({message:'Failed!', error: error});
                        });

                } else {
                    this.$nextTick(() => {
                        const el = formRef.$el.querySelector(".v-messages.error--text:first-of-type");
                        this.$vuetify.goTo(el, {offset: 75});
                    });
                }
            },

            initialize() {
                if (this.shop || this.shopFromTable) {
                    let self = this;
                    /*if(this.tabs === 'opening-hours'){
                        this.$refs.hoursForm.reset();
                    }
                    if(this.tabs === 'delivery-hours'){
                        this.$refs.deliveryForm.reset();
                    }*/
                    this.days = {
                        'Monday': {
                            timeSlots: [{}],
                            delivery: [{}],
                            isOpen: false,
                            isDelivery: false
                        },
                        'Tuesday': {
                            timeSlots: [{}],
                            delivery: [{}],
                            isOpen: false,
                            isDelivery: false
                        },
                        'Wednesday': {
                            timeSlots: [{}],
                            delivery: [{}],
                            isOpen: false,
                            isDelivery: false
                        },
                        'Thursday': {
                            timeSlots: [{}],
                            delivery: [{}],
                            isOpen: false,
                            isDelivery: false
                        },
                        'Friday': {
                            timeSlots: [{}],
                            delivery: [{}],
                            isOpen: false,
                            isDelivery: false
                        },
                        'Saturday': {
                            timeSlots: [{}],
                            delivery: [{}],
                            isOpen: false,
                            isDelivery: false
                        },
                        'Sunday': {
                            timeSlots: [{}],
                            delivery: [{}],
                            isOpen: false,
                            isDelivery: false
                        }
                    }
                    if(this.getShop.workHours.length > 0){
                        console.log(this.getShop.workHours);
                        this.getShop.workHours.forEach(function (wh) {
                            // console.log(wh);
                            var weekday = new Array(7);
                            weekday[0] = "Sunday";
                            weekday[1] = "Monday";
                            weekday[2] = "Tuesday";
                            weekday[3] = "Wednesday";
                            weekday[4] = "Thursday";
                            weekday[5] = "Friday";
                            weekday[6] = "Saturday";
                            // console.log(self.days[weekday[wh.day]]);
                            if (wh.type === 'opening') {
                                if (self.days[weekday[wh.day]].timeSlots[0].from === undefined
                                    || self.days[weekday[wh.day]].delivery[0].to === undefined) {
                                    self.days[weekday[wh.day]].timeSlots = [];
                                    self.days[weekday[wh.day]].isOpen = wh.is_open;
                                }
                                self.days[weekday[wh.day]].timeSlots.push({
                                    from: wh.opening_hour,
                                    to: wh.closing_hour
                                })
                            }
                            else if(wh.type === 'delivery'){
                                if (self.days[weekday[wh.day]].delivery[0].from === undefined
                                    || self.days[weekday[wh.day]].delivery[0].to === undefined) {
                                    self.days[weekday[wh.day]].delivery = [];
                                    self.days[weekday[wh.day]].isDelivery = wh.is_open;
                                }
                                self.days[weekday[wh.day]].delivery.push({
                                    from: wh.opening_hour,
                                    to: wh.closing_hour
                                })
                            }
                        });
                    }
                    for (let index in this.getShop.order_sources) {
                        axios.get('/web/v1/order-sources/' + this.getShop.order_sources[index].id).then(function (response) {
                            self.$set(self.getShop.order_sources[index], 'credentials', response.data.data.credentials)
                            self.$set(self.showPassword, index, false)
                        })
                    }
                }
            },

            createPosToken(params) {
                this.posApiLoading = true;
                var self = this;
                axios.get('/web/v1/auth/token/issue').then(function (response) {
                    if (response.hasOwnProperty('data') && !!response.data) {
                        params.ref.credentials.token = response.data;
                        let dataUpdate = {
                            id: params.ref.id,
                            is_active: params.ref.is_active,
                            is_auto_accept: params.ref.is_auto_accept,
                            credentials: params.ref.credentials
                        }

                        axios.put('/web/v1/order-sources/' + params.ref.id, dataUpdate).then(function (response) {
                            self.showSuccess('Save completed successfully');
                            self.posApiLoading = false;
                        })
                    }
                })
            },

            refreshPosToken(params) {
                this.deletePosToken(params, true)
            },

            deletePosToken(params, create = false) {
                console.log('params.ref.credentials', params.ref.credentials);
                this.posApiLoading = true;
                var self = this;
                fetch('/api/v1/auth/token/revoke', {
                    method: 'get',
                    credentials: 'omit',
                    // body: 'grant_type=client_credentials&client_id=' + key + '&client_secret=' + secret,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Authorization': 'Bearer ' + params.ref.credentials.token,
                        "cache-control": "no-cache"
                    }
                }).then(function (resp) {
                    if (resp.status === 200) {
                        params.ref.credentials.token = '';
                        let dataUpdate = {
                            id: params.ref.id,
                            is_active: params.ref.is_active,
                            is_auto_accept: params.ref.is_auto_accept,
                            credentials: params.ref.credentials
                        }

                        axios.put('/web/v1/order-sources/' + params.ref.id, dataUpdate).then(function (response) {
                            self.showSuccess('Save completed successfully');

                            if (create) {
                                self.createPosToken(params);

                            } else {
                                self.posApiLoading = false;
                            }
                        })
                    }
                });
            },
            syncUberEatsMenu: function (r_id) {
                var _this = this;
                this.syncingMenu = true;
                let data = {
                    shop_id: this.shop.id,
                    source_code: 'ubereats',
                }
                axios.post('/web/v1/store/menu/sync', data).then(function (response) {
                    console.log(response);
                    _this.syncingMenu = false;
                    _this.showSuccess('Save completed successfully');
                })
                    .catch(function (error) {
                        console.log(error);
                        _this.syncingMenu = false;
                        _this.showError({message:'Failed!', error: error});
                    });
            }
        },

        created() {
            if (this.shop || this.shopFromTable) {
                this.initialize();
            }
        },

        watch: {
            shop (newVal, oldVal) {
                if (newVal !== oldVal && !!this.shop) {
                    this.forceShop = true;
                    this.initialize(!!this.shop);
                }
            }
        },
    }
</script>

<style scoped>
    .theme--light.v-sheet--outlined {
        border: thin solid rgba(0,0,0,.15);
    }

    .pc-24-22 {
        padding-right: 22px !important;
        padding-left: 24px !important;
        padding-bottom: 80px;
    }

    .action-card-api {
        position: absolute;
        bottom: 0;
        right: 0;
    }
</style>
