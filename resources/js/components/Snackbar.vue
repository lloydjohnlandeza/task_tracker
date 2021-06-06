<template>
    <v-snackbar
      v-model="snackbar"
    >
      {{ text }}
    </v-snackbar>
</template>

<script>
export default {
  name: 'Confirm',
  data: () => ({
    color: '',
    text: '',
    snackbar: false,
  }),
  methods: {
    open(text, color) {
      this.snackbar = true
      this.text = text
      this.color = color
    },
  },
}
</script>