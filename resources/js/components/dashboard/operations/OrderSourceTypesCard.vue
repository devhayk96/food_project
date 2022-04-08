<template>
  <v-card>
    <v-card-title>
      Order Source Types
      <v-btn
        v-if="userCan('order_types-create')"
        class="mx-2"
        dark
        outlined
        color="primary"
        @click="createdOrderSourceTypeEvent()"
      >
        <v-icon dark> mdi-plus </v-icon>
        Add Order Source Type
      </v-btn>
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
      :items="dataList === '' ? [] : dataList"
      :search="search"
      :loading="dataList === ''"
      :footer-props="{
        'items-per-page-options': [10, 20, 50, -1],
      }"
      :items-per-page="itemsPerPage"
    >
      <template v-slot:item.id="{ item, index }">
        {{ index + 1 }}
      </template>
      <template v-slot:item.is_active="{ item }">
        <v-icon v-if="item.is_active === 0" class="red--text">
          mdi-close
        </v-icon>
        <v-icon v-if="item.is_active === 1"> mdi-check </v-icon>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
        <v-icon small class="mr-2" @click="copyItem(item)">
          mdi-content-copy
        </v-icon>
        <v-icon small class="mr-2" @click="delRecord(item)">
          mdi-delete
        </v-icon>
      </template>
    </v-data-table>
    <confirm-dlg ref="confirm" />
  </v-card>
</template>

<script>
import mixin from "../../../helpers/mixin";
import ConfirmDlg from "../../dialogs/ConfirmDlg";

export default {
  props: ["refresh"],
  mixins: [mixin],

  components: {
    ConfirmDlg,
  },

  data() {
    return {
      search: "",
      headers: [
        {
          text: "Poshub ID",
          align: "start",
          value: "id",
        },
        { text: "POS Code", value: "code" },
        { text: "Name", value: "name" },
        { text: "Client", value: "client_class" },
        { text: "Credentials", value: "credentials_class" },
        { text: "Active", value: "is_active" },
        { text: "Actions", value: "actions" },
      ],
      dataList: "",
      itemsPerPage: 10,
    };
  },

  created() {
    this.initialize();
    // set number of lines
    this.itemsPerPage = +this.getNumberOfLines() || 10;
  },

  methods: {
    async initialize() {
      let self = this;
      await axios
        .get("/web/v1/order-source-types")
        .then(function (response) {
          console.log(response.data.data);
          self.dataList = response.data.data;
        })
        .catch(function (err) {
          console.log(err);
        });
    },

    async delRecord(item) {
      if (
        await this.$refs.confirm.open(
          "Confirm",
          "Are you sure you want to delete this record?"
        )
      ) {
        this.deleteRecord(item);
      }
    },

    async deleteRecord(item) {
      await axios
        .delete(`/web/v1/remove-order-source-type/${item.id}`)
        .then((response) => {
          if (response.status === 200) {
            this.initialize().then((res) => {
              this.$emit("refreshed", {
                lengthNow: this.dataList.length,
                progress: "delete",
              });
            });
          }
        });
    },

    createdOrderSourceTypeEvent() {
      this.$emit("createEvent", {
        event: "create-source-type",
        eventsCountNow: this.dataList.length,
      });
    },

    editItem(item) {
      this.$emit("editEvent", { event: "edit-source-type", editable: item });
    },

    async copyItem(item) {
      await axios
        .post("/web/v1/copy-order-source-type", {
          id: item.id,
        })
        .then((response) => {
          if (response.status === 200) {
            this.initialize().then((res) => {
              this.$emit("refresh", {
                lengthNow: this.dataList.length,
                progress: "copy",
              });
              this.$emit("refreshed", {
                lengthNow: this.dataList.length,
                progress: "copy",
              });
            });
          }
        });
    },
  },

  watch: {
    async refresh() {
      console.log(this.refresh);
      await this.initialize();
      this.$emit("refreshed", {
        lengthNow: this.dataList.length,
        progress: this.refresh.e,
      });
    },
  },
};
</script>
