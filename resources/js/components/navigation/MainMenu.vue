<template>
    <v-list nav dense>
        <div
            v-if="userCan(item.permissions)"
            v-for="(item, index) in menu" :key="index"
            v-show="!!shop || item.noShopSelected"
        >
            <div v-if="item.key || item.text" class="pa-1 mt-2 overline">{{ item.key ? $t(item.key) : item.text }}</div>
            <nav-menu :menu="item.items"/>
        </div>
    </v-list>
</template>

<script>
import NavMenu from './NavMenu'
import { mapState } from "vuex";

export default {
    components: {
        NavMenu
    },
    props: {
        menu: {
            type: Array,
            default: () => []
        }
    },
    computed: {
        ...mapState('app', ['shop']),
    },
}
</script>
