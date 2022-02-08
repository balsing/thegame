<template>
  <div class="hello">
    <h1>{{ this.room.code }}</h1>
    <h2>{{ this.room.status.title }}</h2>
    <div>
      <h3>Подключенные игроки:</h3>
      <ul id="example-1">
        <li v-for="player in this.room.players" :key="player.uuid">
          {{ player.name }} <span v-if="player.isOwner === false">НЕ Владелец</span><span v-if="player.isOwner === true">Владелец</span>
        </li>
      </ul>
    </div>
    <p>
      <input type="submit" v-on:click="start" name="start" value="Начать">
    </p>
  </div>
</template>

<script>
export default {
  name: 'RoomPage',
  data () {
    return {
      message: 'Welcome to Your Vue.js App 77777'
    }
  },
  methods: {
    start: function (event) {
      this.$parent.request('http://localhost/game/start', {
        player: this.$root.$data.player,
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
  beforeCreate() {
    let room;
    room = this.$root.$data.room
    if(!room){
      room = JSON.parse(localStorage.room)
    }
    if(!room){
      this.$router.push({ name: 'MainPage' });
    }
    this.room = room;
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
