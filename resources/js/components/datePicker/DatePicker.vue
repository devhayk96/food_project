<template>
  <div>
    <v-menu
      v-model="menu"
      :close-on-content-click="false"
      :nudge-right="40"
      transition="scale-transition"
      offset-y
      min-width="auto"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-text-field
          v-model="date"
          :label="setLabel"
          prepend-icon="mdi-calendar"
          readonly
          v-bind="attrs"
          v-on="on"
        ></v-text-field>
      </template>
      <v-date-picker v-model="date" @input="dateSelected"></v-date-picker>
    </v-menu>
  </div>
</template>

<script>
export default {
  name: "DatePicker",

  props: {
      setLabel:{
          type:String,
          required:true
      }
  },

  data: () => ({
    menu: false,
    date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      .toISOString()
      .substr(0, 10),
  }),

  methods: {
      dateSelected() {
          this.menu = false;
          this.$emit('date', this.date);
      }
  }
};
</script>
