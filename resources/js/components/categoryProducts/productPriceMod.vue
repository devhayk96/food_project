<template>
  <div class="price-mod">
    <div v-if="mod === 'view'" class="view-mod">
      {{ parseFloat(price).toFixed(2) }}
      <v-icon @click="mod = 'edit enable-bg'" x-small class="ico-style">
        mdi-pencil
      </v-icon>
    </div>
    <div v-else class="editable-price-mod">
      <input v-model="price" />
      <v-icon @click="save" class="specific-size-ico enable-bg">
        mdi-check-circle-outline
      </v-icon>
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";
export default {
  name: "productPriceMod",

  props: {
    product: {
      type: Object,
    },
  },

  mounted() {
    this.price = parseFloat(this.product.price).toFixed(2);
  },

  data: () => ({
    mod: "view",
    price: "",
  }),

  methods: {
    ...mapActions("menuCategories", ["editProductPrice"]),

    save() {
      this.mod = "view";
      this.price = parseFloat(this.price).toFixed(2);
      const data = {
        id: this.product.id,
        price: this.price,
      };
      this.editProductPrice(data);
    },
  },
};
</script>


<style lang="scss" scoped>
.specific-size-ico {
  width: 50%;
  font-size: 15px !important;
}

.view-mod,
.editable-price-mod {
  width: 80px;
  margin: auto;
  height: 31px;
}

.editable-price-mod {
  display: flex;
  align-items: center;
  border-radius: 100px;
  justify-content: space-evenly;
  border: 1px solid #eeeeee;
  background: #ffffff 0% 0% no-repeat padding-box;
}

.editable-price-mod > input {
  width: 50%;
  outline: none;
  max-height: 13px;
  text-align: center;
  border-right: 1px solid #c8c8c8;
}
</style>
