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
        field: undefined,
        condition: 'eq',
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
      if (args !== undefined && args.length > 0) {
        args.every((argParams) => {
          for (const [key, value] of Object.entries(argParams)) {
            if (key in this.tableParams)
              this.tableParams[key] = value;
          }
        });
      }
      this.$http.get(this.url, { params: this.tableParams })
        .then(response => {
            this.$eventBus.$emit('entriesFetched', response.data);
        });
    }
  }

};
</script>
