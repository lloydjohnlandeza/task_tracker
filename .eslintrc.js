module.exports = {
  extends: [
    // add more generic rulesets here, such as:
    'plugin:vue/essential',
    'eslint:recommended',
  ],
  rules: {
    // override/add rules settings here, such as:
    'vue/no-unused-vars': 2,
    'semi': [2, 'never'],
    'quotes': ['warn', 'single'],
    'comma-dangle': ['warn', 'always-multiline'],
  },
}
