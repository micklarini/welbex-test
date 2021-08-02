<template>
    <div class="container">
        <h1>WelbeX test project</h1>
        <DataTable></DataTable>
    </div>
</template>

<script>
export default {
  name: "App",

  props: {},
  data() {
    return {
      url: '/data/entries/',
      tableParams: {
        page: 1,
        pagesize: 25,
        sort: undefined,
        condition: '=',
        filter: undefined,
      },
    }
  },
  beforeMount() {
    this.$eventBus.$on('fetchEntries', this.fetchEntries);
    this.fetchEntries();
  },
  beforeDestroy(){
    this.$eventBus.$off('fetchEntries');
  },
  methods: {
    fetchEntries(...args) {
      let params = this.tableParams;
      const argParams = args.shift();
      if (argParams !== undefined) {
        for (const [key, value] of Object.entries(argParams)) {
          params[key] = value;
        }
      }
console.log(params);
      this.$http.get(this.url, { params })
        .then(response => {
            this.$eventBus.$emit('entriesFetched', response.data.data);
        });
    }
  }

};
</script>
