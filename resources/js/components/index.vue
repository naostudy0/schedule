<template>
  <div v-if="schedule">
    <div v-if="schedule.data.days_have_plan.length === 0">
      <p>予定がありません</p>
    </div>
    <div v-else>
      <div
        v-for="day in schedule.data.days_have_plan"
      >
        <span>{{ day }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      schedule: {},
    };
  },
  mounted: function() {
    this.getSchedule()
  },
  methods: {
    /**
     * 登録された予定を取得
     *
     * @return void
     */
    async getSchedule() {
      this.schedule = await axios.get('/api/schedule/?date=' + this.$route.params.date);
    },
  }
}
</script>