<template>
  <div v-if="shareData">
    <p>share</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      shareData: {},
    };
  },
  mounted: function() {
    this.getShareData()
  },
  methods: {
    /**
     * ユーザー情報を取得
     *
     * @return void
     */
    async getShareData() {
      this.shareData = await axios.get('/api/share/');
    },
  }
}
</script>