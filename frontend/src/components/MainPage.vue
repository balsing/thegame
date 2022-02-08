<template>
  <div class="hello">
    <h1>{{ message }}</h1>
    <form action="" method="post">
      <p>
        <input type="submit" v-on:click="socket" name="socket" value="Тест сокета">
      </p>
    </form>
    <form action="" method="post">
      <p>
        <input type="submit" v-on:click="create" name="enter" value="Создать новую">
      </p>
    </form>
    <p>
      ЛИБО
    </p>
    <form action="" method="post">
      <p>
        <input name="name" v-model="room" placeholder="Номер комнаты"> <input type="submit" v-on:click="join" name="enter" value="Присоединиться">
      </p>
    </form>
  </div>
</template>

<script>
export default {
  name: 'MainPage',
  data () {
    return {
      message: 'MainPage',
      room: ''
    }
  },
  methods: {
    socket: async function (event){
      event.preventDefault()
      if(!this.$root.$data.token){
        let _this = this;
        await this.$parent.request('http://localhost/connect', this.$root.$data.player).then(function (data){
          _this.$root.$data.token = data.token;
          localStorage.token = JSON.stringify(_this.$root.$data.token)
        });
      }

      let token = this.$root.$data.token;
      let user_id = this.$root.$data.player.uuid;
      this.$root.$data.socket.connect(token, user_id);

      return false
    },
    create: function (event) {
      var _this = this;

      this.$parent.request('http://localhost/lobby/create', {player: _this.$root.$data.player})
        .then(function (data){
          _this.$root.$data.room = data.room;
          localStorage.room = JSON.stringify(data.room)
          _this.$router.push({ name: 'RoomPage' });
        });

      event.preventDefault()
      return false
    },
    join: function (event) {
      var _this = this;

      this.$parent.request('http://localhost/lobby/join', {
        player: _this.$root.$data.player,
        room: this.room
      })
        .then(function (data){
          _this.$root.$data.room = data.room;
          localStorage.room = JSON.stringify(data.room)
          _this.$router.push({ name: 'RoomPage' });
        });

      event.preventDefault()
      return false
    }
  },
  beforeMount(){
    if(!this.$parent.checkLogin()){
      this.$router.push({ name: 'LoginPage' });
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
