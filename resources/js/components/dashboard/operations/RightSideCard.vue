<template>
    <v-card
    :class="show ? 'show' : ''"
    class="mx-auto card-pos"
    >
    <v-list-item two-line>
        <v-list-item-content>
        <v-card-title
            class="n-pl-pr flex">
            {{ title }}
            <v-btn
            fab
            x-small
            class="no-shadows"
            @click="close">
                <v-icon small >mdi-close</v-icon>
            </v-btn>
        </v-card-title>
        </v-list-item-content>
    </v-list-item>
    <slot name="cardcontent"></slot>
    </v-card>
</template>

<script>

import CreateEdit from '../../dashboard/OrderSourceTypes/CreateEdit'

export default {

props: [ 'refresh', 'title', "resource", 'showcard'],

components: {
    CreateEdit
},

    data() {
    return {
        show: false
    };
},
    methods: {

    close() {
        this.show = false;
        this.$emit('closed', true);
    }
},

    watch: {
        showcard() {
            this.show = true;
        },

        refresh() {
            this.title = this.title;
        }
    }
};
</script>

<style scoped>
    .card-pos {
        width:0;
        height:100vh;
        overflow: hidden;
        max-width: 465px;
        transition: width 0.5s;
        margin-left: 16px !important;
    }

    .show {
        width:465px;
    }

    .flex {
        display: flex;
        align-items: center;
        justify-content:space-between;
    }

    .no-shadows {
        box-shadow: none !important;
    }

    .n-pl-pr {
        padding-right:0 !important;
        padding-left:0 !important;
    }
</style>
