import Vue from 'vue'
import Vuex from 'vuex'

// Global vuex
import AppModule from './app';
import menuList from './menuList';
import customers from './customers';
import optionGroup from './optionGroup';
import device from './device';
import shop from './shop';
import menuCategories from './menuCategories';
import settings from  './settings';


Vue.use(Vuex)

/**
 * Main Vuex Store
 */
const store = new Vuex.Store({
    modules: {
        menuList,
        settings,
        customers,
        optionGroup,
        device,
        shop,
        app: AppModule,
        menuCategories,
    }
})

export default store
