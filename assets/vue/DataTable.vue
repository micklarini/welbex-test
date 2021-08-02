<template>
<div class="datatable-container">
  <div class="filter">
    <select data-field="field" @change="optionsChanged($event)">
        <option value="date">Дата</option>
        <option value="name">Название</option>
        <option value="quantity">Количество</option>
        <option value="distance">Расстояние</option>
    </select>
    <select data-field="condition" @change="optionsChanged($event)">
      <option value="eq">равно</option>
      <option value="gt">больше</option>
      <option value="lt">меньше</option>
    </select>
    <input data-field="filter" type="text"  @keyup="startTimer($event)" placeholder="Значение фильтра" />
  </div>
  <div class="pager" v-if="pages > 0">
    <span v-for="page in pages" :class="currentPage == page ? 'current' : 'link'" v-on:click.prevent="clickLink($event)" data-field="page" :data-val="page">
      {{ page }}
    </span>
  </div>
  <table class="data-entries">
    <thead>
      <tr>
        <th>Дата</th>
        <th class="sortable" @click="clickLink($event)" data-field="sort" data-val="name">Название</th>
        <th class="sortable" @click="clickLink($event)" data-field="sort" data-val="quantity">Количество</th>
        <th class="sortable" @click="clickLink($event)" data-field="sort" data-val="distance">Расстояние</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="entry in dataEntries" :key="entry.id">
        <td>{{ entry.EntryDate | asDate }}</td>
        <td>{{ entry.Name }}</td>
        <td>{{ entry.Quantity }}</td>
        <td>{{ entry.Distance }}</td>
      </tr>
    </tbody>
  </table>
</div>
</template>

<script>

export default {
  name: 'DataTable',

  data() {
    return {
      filterKeys: [
        'field',
        'condition',
        'filter',
      ],

      dataEntries: [],
      pages: 0,
      currentPage: undefined,
    }
  },
  props: {},
  beforeMount() {
    this.$eventBus.$on('entriesFetched', this.entriesFetched);
    this.$eventBus.$on('clickLink', this.clickLink);
  },
  beforeDestroy(){
    this.$eventBus.$off('entriesFetched');
    this.$eventBus.$off('clickLink');
  },
  methods: {
    clickLink(event) {
      let params = {}
      params[event.target.dataset.field] = event.target.dataset.val;
      this.makePopup(event);
      this.$eventBus.$emit('fetchEntries', params);
    },
    entriesFetched(entries = []) {
      this.dataEntries = entries.data.items;
      this.pages = entries.data.pages;
      this.currentPage = entries.query.page;
      this.closePopup();
    },
    optionsChanged() {
      this.stopTimer();
      this.$eventBus.$emit('fetchEntries', {page: 1, field: undefined, filter: undefined});
      if (event.target.dataset.field == 'field') {
        document.querySelector('[data-field="condition"]').value = 'eq';
        document.querySelector('[data-field="filter"]').value = '';
      }
    },
    startTimer(event) {
      this.stopTimer();
      this.makePopup(event);
      let dataFilter = {};

      const selector = this.filterKeys.map((val) => `.filter > [data-field="${val}"]`).join(', ');
      let items = document.querySelectorAll(selector);

      for (const item of items) {
        dataFilter[item.dataset.field] = item.value !== '' ? item.value : undefined;
      }

      this.timer = setTimeout(
        (params) => {
//
          this.$eventBus.$emit('fetchEntries', params);
        }, 
        700,
        dataFilter
      );
    },
    stopTimer() {
      if (this.timer !== undefined) {
        clearTimeout(this.timer);
        this.timer = undefined;
      }
    },
    makePopup(event) {
      this.popTarget = $(event.target).popover({content: 'Пожалуйста подождите'}).popover('show');
    },
    closePopup() {
      if (this.popTarget !== undefined) {
        this.popTarget.popover('dispose');
        this.popTarget = undefined;
      }
    }
  },
}
</script>
