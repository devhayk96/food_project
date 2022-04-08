<template>
  <v-container>
    <v-card min-height="660" width="100%">
      <v-form ref="form">
        <v-card-title class="text-capitalize">
          <v-btn
            class="mx-2"
            dark
            outlined
            color="primary"
            @click="$router.go(-1)"
          >
            <v-icon small dark> mdi-arrow-left </v-icon>
            &nbsp; Go Back
          </v-btn>
        </v-card-title>
        <v-card-text class="mt-2">
          <div style="width: 60%">
            <div class="custom--container">
              <div>
                <div>
                  <v-card-title class="card-subtitle">
                    {{ this.dataDetails.id > 0 ? "Edit" : "Add" }} Category
                  </v-card-title>
                </div>
                <div>
                  <v-text-field
                    label="Name"
                    name="name"
                    type="text"
                    v-model="dataDetails.name"
                    :rules="textRules"
                    required
                  ></v-text-field>
                </div>

                <div>
                  <v-textarea
                    rows="1"
                    required
                    no-resize
                    type="textarea"
                    :rules="textRules"
                    name="description"
                    label="Description"
                    v-model="dataDetails.description"
                  ></v-textarea>
                </div>

                <div>
                  <v-checkbox
                    v-model="dataDetails.is_blocked"
                    inset
                    :label="`Blocked`"
                  ></v-checkbox>
                </div>
                <div>
                  <v-checkbox
                    v-model="dataDetails.is_food"
                    inset
                    :label="`Food`"
                  ></v-checkbox>
                </div>
                <div>
                  <v-checkbox
                    v-model="dataDetails.is_drink"
                    inset
                    :label="`Drink`"
                  ></v-checkbox>
                </div>

                <div
                  v-if="
                    dataDetails.id > 0 &&
                    dataDetails.image !== null &&
                    dataDetails.image.trim() !== ''
                  "
                >
                  <label class="text--secondary">
                    <v-icon> mdi-camera </v-icon>
                    Image
                  </label>
                  <div class="d-flex">
                    <div cols="4">
                      <v-img
                        max-height="160"
                        max-width="300"
                        :src="'../../' + dataDetails.image"
                      />
                    </div>
                    <div cols="4">
                      <v-btn
                        class="mx-2"
                        outlined
                        color="red"
                        @click="
                          () => {
                            dataDetails.image = null;
                          }
                        "
                        >Remove</v-btn
                      >
                    </div>
                  </div>
                </div>
                <div v-else>
                  <v-file-input
                    label="Upload Image"
                    name="image"
                    append-icon="mdi-upload"
                    prepend-icon=""
                    v-model="dataDetails.image"
                    accept="image/png, image/jpeg, image/jpg"
                  ></v-file-input>
                  <v-card-actions class="float-left mt-3 pl-0">
                    <v-btn text rounded class="mr-4 pl-0" @click="$router.go(-1)">
                      &nbsp;CANCEL
                    </v-btn>
                    <v-btn
                      v-if="userCan(savePermission)"
                      dark
                      rounded
                      min-width="160"
                      color="primary"
                      @click="saveDetails"
                    >
                      SAVE
                    </v-btn>
                  </v-card-actions>
                </div>
              </div>
            </div>
          </div>
        </v-card-text>
      </v-form>
    </v-card>
  </v-container>
</template>

<script>
import { mapActions } from "vuex";

export default {
  name: "MenuCategoryManage",
  props: ["id"],
  components: {},
  data() {
    return {
      dataDetails: {
        id: 0,
        name: "",
        description: "",
        is_blocked: 0,
        is_food: 0,
        is_drink: 0,
        image: null,
      },
      textRules: [(v) => !!v || "Field is required"],
      savePermission: "menu_categories-create",
    };
  },
  created() {
    if (this.id === undefined) {
      this.dataDetails.id = this.$route.query.id;
    } else {
      this.dataDetails.id = this.id;
    }
  },
  mounted() {
    console.log(this.dataDetails.id);
    if (this.dataDetails.id > 0) {
      this.savePermission = "menu_categories-update";
      this.fetchDetails();
    }
  },

  beforeDestroy() {},

  methods: {
    ...mapActions("app", ["showSuccess", "showError"]),
    fetchDetails: function () {
      const _this = this;
      axios
        .get("/web/v1/menu-category/" + this.dataDetails.id)
        .then(function (response) {
          console.log(response);
          _this.dataDetails = response.data;
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    saveDetails: function () {
      const _this = this;
      if (this.$refs.form.validate()) {
        if (_this.dataDetails.id > 0) {
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
        .post("/web/v1/menu-category", formData)
        .then(function (response) {
          console.log(response);
          if (response.data) {
            _this.showSuccess("Data is updated.");
            _this.$refs.form.reset();
            _this.$refs.form.resetValidation();
            _this.$router.push({ name: "menu-category-list" });
          } else {
            _this.showError({
              message: "Error : ",
              error: { message: "Something Went Wrong." },
            });
          }
        })
        .catch(function (error) {
          console.log(error);
          _this.showError({ message: "Error: ", error: error });
        });
    },
    updateData: function () {
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
      // formData.append('_method', 'POST');
      axios
        .post("/web/v1/menu-category/" + this.dataDetails.id, formData)
        .then(function (response) {
          console.log(response);
          if (response.data) {
            _this.showSuccess("Data is updated.");
            _this.$refs.form.reset();
            _this.$refs.form.resetValidation();
            _this.$router.push({ name: "menu-category-list" });
          } else {
            _this.showError({
              message: "Error : ",
              error: { message: "Something Went Wrong." },
            });
          }
        })
        .catch(function (error) {
          console.log(error);
          _this.showError({ message: "Error: ", error: error });
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.card-subtitle {
  padding: 0 !important;
  padding-bottom: 5px !important;
  padding-top: 5px !important;
}

.custom--container {
  width: 42.333%;
  margin: auto;
}

.v-input--selection-controls {
    margin:0 !important;
}
</style>
