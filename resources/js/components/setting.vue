<template>
  <div v-if="user">
    <ul>
      <li>{{ user.data.name }}</li>
      <li>******（表示されません）</li>
      <li>{{ user.data.email }}</li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: {},
    };
  },
  mounted: function() {
    this.getUser()
  },
  methods: {
    /**
     * ユーザー情報を取得
     *
     * @return void
     */
    async getUser() {
      this.user = await axios.get('/api/setting/');
    },
  }
}
</script>