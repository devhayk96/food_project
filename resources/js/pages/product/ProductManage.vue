<template>
  <v-card class="px-2 py-2">
    <v-container
      style="height: 400px"
      v-if="$route.query.action === 'edit' && dataDetails.name === ''"
    >
      <v-row class="fill-height" align-content="center" justify="center">
        <v-col class="subtitle-1 text-center" cols="12">
          Fetching data...
        </v-col>
        <v-col cols="6">
          <v-progress-linear
            color="primary accent-4"
            indeterminate
            rounded
            height="6"
          ></v-progress-linear>
        </v-col>
      </v-row>
    </v-container>
    <v-form ref="form" v-else>
      <v-card-title class="text-capitalize">
        <v-btn dark outlined color="primary" @click="$router.go(-1)">
          <v-icon small dark> mdi-arrow-left </v-icon>
          &nbsp; Go Back
        </v-btn>
      </v-card-title>

      <v-card-title class="text-capitalize px-lg-16 pb-5">
        <h3 class="font-weight-medium">{{ $route.query.action }} Product</h3>
      </v-card-title>

      <v-card-text class="px-lg-16">
        <v-row>
          <v-col class="col-12 col-md-6 col-lg-6 pe-lg-7">
            <v-row>
              <v-col cols="12" md="12" class="py-0">
                <v-text-field
                  label="Article Number"
                  name="article_number"
                  type="text"
                  v-model="dataDetails.article_number"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="py-0">
                <v-text-field
                  label="Name"
                  name="name"
                  type="text"
                  v-model="dataDetails.name"
                  :rules="textRules"
                  required
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="12" class="py-0">
                <v-text-field
                  label="Description 2"
                  name="description_2"
                  type="textarea"
                  v-model="dataDetails.description_2"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="12" class="py-0">
                <v-text-field
                  label="Description 3"
                  name="description_3"
                  type="textarea"
                  v-model="dataDetails.description_3"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="12" class="py-0">
                <v-text-field
                  label="Price"
                  name="price"
                  type="number"
                  v-model="dataDetails.price"
                  :rules="textRules"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="py-0">
                <v-text-field
                  label="Course"
                  name="course"
                  type="number"
                  v-model="dataDetails.course"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="12" class="py-0">
                <v-text-field
                  label="EAN"
                  name="ean"
                  type="textarea"
                  v-model="dataDetails.ean"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="12" class="py-0">
                <v-select
                  label="Subgroup"
                  name="subcategory"
                  :loading="productSubgroupList === ''"
                  :items="
                    _.map(productSubgroupList, (subg) => {
                      return {
                        text: subg.product_groups.id + '-' + subg.name,
                        value: subg.id,
                      };
                    })
                  "
                  v-model="dataDetails.product_subgroup_id"
                  :rules="textRules"
                  required
                ></v-select>
              </v-col>

              <v-col cols="12" md="12" class="py-0">
                <v-select
                  label="Vat"
                  name="vat"
                  :loading="vatList === ''"
                  :items="
                    _.map(vatList, (vat) => {
                      return { text: vat.name, value: vat.id };
                    })
                  "
                  v-model="dataDetails.vat_id"
                ></v-select>
              </v-col>

              <v-col cols="12" md="12" class="py-0">
                <v-select
                  label="Stock Status"
                  name="status"
                  :items="[
                    { text: 'In Stock', value: 1 },
                    { text: 'Out Of Stock', value: 0 },
                  ]"
                  v-model="dataDetails.status"
                  required
                ></v-select>
              </v-col>
            </v-row>
          </v-col>
          <v-col class="col-12 col-md-6 col-lg-4">
            <v-row>
              <v-col cols="12" sm="6" class="py-0">
                <v-checkbox
                  v-model="dataDetails.main_product"
                  inset
                  :label="`Main Product`"
                ></v-checkbox>
              </v-col>

              <v-col cols="12" sm="6" class="py-0">
                <v-checkbox
                  v-model="dataDetails.is_receipt"
                  inset
                  :label="`Receipt`"
                ></v-checkbox>
              </v-col>

              <v-col cols="12" sm="6" class="py-0">
                <v-checkbox
                  v-model="dataDetails.is_kitchen"
                  inset
                  :label="`Kitchen`"
                ></v-checkbox>
              </v-col>

              <v-col cols="12" sm="6" class="py-0">
                <v-checkbox
                  v-model="dataDetails.is_sticker"
                  inset
                  :label="`Sticker`"
                ></v-checkbox>
              </v-col>
              <v-col cols="12" sm="6" class="py-0">
                <v-checkbox
                  v-model="dataDetails.is_blocked"
                  inset
                  :label="`Blocked`"
                ></v-checkbox>
              </v-col>
              <v-col cols="12" sm="6" class="py-0">
                <v-checkbox
                  v-model="dataDetails.is_various"
                  inset
                  :label="`Various`"
                ></v-checkbox>
              </v-col>
              <v-col cols="12" sm="6" class="py-0">
                <v-checkbox
                  v-model="dataDetails.is_open_number"
                  inset
                  :label="`Open Number`"
                ></v-checkbox>
              </v-col>
              <v-col cols="12" sm="6" class="py-0">
                <v-checkbox
                  v-model="dataDetails.is_open_price"
                  inset
                  :label="`Open Price`"
                ></v-checkbox>
              </v-col>
              <v-col cols="12" sm="6" class="py-0">
                <v-checkbox
                  v-model="dataDetails.restock"
                  inset
                  :label="`Restock`"
                ></v-checkbox>
              </v-col>

              <!-- <v-col cols="12" md="12" class="py-0" v-if="dataDetails.image !== null && dataDetails.image.trim() !== ''">
                                <label class="text--secondary">
                                    <v-icon>
                                        mdi-camera
                                    </v-icon>
                                    Upload Image
                                </label>
                                <div class="d-flex">
                                    <v-col cols="4" class="py-0">
                                        <v-img
                                            max-height="160"
                                            max-width="300"
                                            :src="dataDetails.image"
                                        />
                                    </v-col>
                                    <v-col cols="4" class="py-0">
                                        <v-btn
                                            class="mx-2"
                                            outlined
                                            color="red"
                                            @click="()=>{dataDetails.image = null}">Remove</v-btn>
                                    </v-col>
                                </div>
                            </v-col>
                            <v-col cols="12" md="12" v-else>
                                <v-file-input
                                    label="Upload Image"
                                    name="image"
                                    v-model="dataDetails.image"
                                    accept="image/png, image/jpeg, image/jpg"
                                    prepend-icon="mdi-camera"
                                ></v-file-input>
                            </v-col> -->

              <v-col cols="12">
                <div class="photo-upload">
                  <!-- *************************************************************
                                *                 DropZone Component Events                      *
                                *      start,end,drag-drop,file-added,file-removed,success       *
                                *                                                                *
                                ****************************************************************** -->
                  <dropzone
                    @removed="imgRemoved"
                    @success="dragSuccess"
                    :manuallfyAddedFile="
                      this.$route.query.action === 'edit'
                        ? dataDetails.image
                        : ''
                    "
                  />
                </div>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
        <v-row> </v-row>
      </v-card-text>

      <v-card-actions class="float-left mt-5 mt-lg-10 px-lg-16 pb-lg-10">
        <v-btn
          rounded
          text
          left
          large
          color="black"
          class="mr-5"
          plain
          @click="$router.go(-1)"
        >
          CANCEL
        </v-btn>
        <v-btn
          v-if="userCan(savePermission)"
          dark
          rounded
          bottom
          right
          large
          color="primary"
          min-width="160"
          @click="saveDetails"
        >
          SAVE
        </v-btn>
      </v-card-actions>
    </v-form>
  </v-card>
</template>

<script>
import { mapActions } from "vuex";
import Dropzone from "@/components/dropzone/vueDropzone";

export default {
  name: "ProductManage",
  props: ["id"],
  components: {
    Dropzone,
  },
  data() {
    return {
      dataDetails: {
        id: 0,
        article_number: "",
        vat_id: "",
        product_subgroup_id: "",
        name: "",
        description_2: "",
        description_3: "",
        status: 1,
        price: 0,
        main_product: 1,
        ean: "",
        is_receipt: 0,
        is_kitchen: 0,
        is_sticker: 0,
        is_blocked: 0,
        is_various: 0,
        is_open_number: 0,
        is_open_price: 0,
        restock: 0,
        image: null,
        course: 0,
      },
      imageData: "",
      vatList: "",
      productSubgroupList: "",
      textRules: [(v) => !!v || "Field is required"],
      savePermission: "products-create",
    };
  },
  created() {
    if (
      this.$route.query.action === "add" ||
      (this.$route.query.action === "edit" && this.$route.query.id)
    ) {
      console.log("working");
    } else {
      this.showError({
        message: "Error : ",
        error: { message: "The query params are incorrect." },
      });
      this.$router.go(-1);
    }
  },
  mounted() {
    if (this.$route.query.action === "edit") {
      this.savePermission = "products-update";
      this.fetchDetails();
    }
    this.fetchProductSubgroupList();
    this.fetchVatList();
  },

  beforeDestroy() {},

  methods: {
    ...mapActions("app", ["showSuccess", "showError"]),
    fetchProductSubgroupList: function () {
      const _this = this;
      axios
        .get("/web/v1/subgroup")
        .then(function (response) {
          console.log(response);
          _this.productSubgroupList = response.data.data;
        })
        .catch(function (error) {
          console.log(error);
          _this.showError({ message: "Error : ", error: error });
        });
    },
    fetchVatList: function () {
      const _this = this;
      axios
        .get("/web/v1/vat")
        .then(function (response) {
          console.log(response);
          _this.vatList = response.data.data;
        })
        .catch(function (error) {
          console.log(error);
          _this.showError({ message: "Error : ", error: error });
        });
    },
    fetchDetails: function () {
      const _this = this;
      axios
        .get("/web/v1/product/" + this.$route.query.id)
        .then(function (response) {
          console.log(response);
          _this.dataDetails = response.data;
          _this.dataDetails.product_subgroup_id =
            response.data.product_subgroups.id;
          _this.dataDetails.vat_id = response.data.vats
            ? response.data.vats.id
            : null;
        })
        .catch(function (error) {
          console.log(error);
          _this.showError({ message: "Error : ", error: error });
        });
    },
    saveDetails: function () {
      const _this = this;
      if (this.$refs.form.validate()) {
        console.log(this.dataDetails);
        if (_this.$route.query.action === "edit") {
          this.updateData();
        } else {
          this.addData();
        }
      } else {
        _this.showError({
          message: "Error : ",
          error: { message: "Please fill the mandatory fields." },
        });
      }
    },
    addData: function () {
      const _this = this;
      const formData = new FormData();
      Object.keys(this.dataDetails).forEach((key) => {
        formData.append(
          key,
          this.dataDetails[key] === null
            ? ""
            : this.dataDetails[key] === true
            ? 1
            : this.dataDetails[key]
        );
      });
      axios
        .post("/web/v1/product", formData)
        .then(function (response) {
          console.log(response);
          if (response.data) {
            _this.showSuccess("Data is updated.");
            _this.$refs.form.reset();
            _this.$refs.form.resetValidation();
            _this.$router.push({ name: "products" });
          } else {
            _this.showError({
              message: "Error : ",
              error: { message: "Something Went Wrong." },
            });
          }
        })
        .catch(function (error) {
          console.log(error);
          _this.showError({ message: "Error : ", error: error });
        });
    },
    updateData: function () {
      const _this = this;
      const formData = new FormData();
      Object.keys(this.dataDetails).forEach((key) => {
        console.log(this.dataDetails[key]);
        formData.append(
          key,
          this.dataDetails[key] === null
            ? ""
            : this.dataDetails[key] === true
            ? 1
            : this.dataDetails[key]
        );
      });
      axios
        .post("/web/v1/product/" + this.dataDetails.id, formData)
        .then(function (response) {
          console.log(response);
          if (response.data) {
            _this.showSuccess("Data is updated.");
            _this.$refs.form.reset();
            _this.$refs.form.resetValidation();
            _this.$router.push({ name: "products" });
          } else {
            _this.showError({
              message: "Error : ",
              error: { message: "Something Went Wrong." },
            });
          }
        })
        .catch(function (error) {
          console.log(error);
          _this.showError({ message: "Error : ", error: error });
        });
    },

    dragSuccess(value) {
      this.dataDetails.image = value.file;
    },

    imgRemoved(value) {
      console.log(this.dataDetails, "removed");
      this.dataDetails.image = "";
    },

    previewThumbnail: function getPreview(event) {
      const input = event.target;
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imageData = e.target.result;
        };
        this.dataDetails.image = input.files[0];
        reader.readAsDataURL(input.files[0]);
      }
    },
  },
};
</script>
<style>
.photo-upload {
  width: 300px;
  height: 200px;
  background-color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}
.photo-upload label {
  display: block;
  color: #0096c7;
  text-align: center;
}
.photo-upload .input-file {
  height: 0.1px;
  opacity: 0;
  overflow: hidden;
  position: absolute;
  width: 0.1px;
  z-index: -1;
}
.image-preview {
  text-align: center;
}
.image-preview img {
  max-width: 100%;
  width: auto;
  height: 80px;
}
</style>
