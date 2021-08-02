<template>
  <table class="data-entries">
    <thead>
      <tr>
        <th>Дата</th>
        <th class="sortable" @click="clickSort($event)" data-field="name">Название</th>
        <th class="sortable" @click="clickSort($event)" data-field="quantity">Количество</th>
        <th class="sortable" @click="clickSort($event)" data-field="distance">Расстояние</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="entry in dataEntries" :key="entry.id">
        <td>{{ entry.EntryDate }}</td>
        <td>{{ entry.Name }}</td>
        <td>{{ entry.Quantity }}</td>
        <td>{{ entry.Distance }}</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  name: 'DataTable',

  data() {
    return {
      dataEntries: []
    }
  },
  props: {},
  beforeMount() {
    this.$eventBus.$on('entriesFetched', this.entriesFetched);
  },
  beforeDestroy(){
    this.$eventBus.$off('entriesFetched');
  },
  methods: {
    clickSort(event) {
      this.$eventBus.$emit('fetchEntries', {sort: event.currentTarget.dataset.field});
    },
    entriesFetched(entries = []) {
      this.dataEntries = entries;
    }
  },
}
</script>
