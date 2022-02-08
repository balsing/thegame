<template>
  <div class="hello">
    <h1>{{ message }}</h1>
    <form action="" method="post">
      <p>
      <label>
        Введите свой ник
        <input name="name" v-model="name">
      </label>
      </p>
      <p>
        <input type="submit" v-on:click="login" name="enter" value="Войти">
      </p>
    </form>
  </div>
</template>

<script>
export default {
  name: 'LoginPage',
  data () {
    return {
      message: 'Welcome to Your Vue.js App 77777',
      name: ''
    }
  },
  methods: {
    login: function (event) {

      var _this = this;
      this.$parent.request('http://localhost/login', {name: this.name})
        .then(function (data){
          _this.$root.$data.player = data.player;
          localStorage.player = JSON.stringify(_this.$root.$data.player)
          _this.$root.$data.token = data.token;
          localStorage.token = JSON.stringify(_this.$root.$data.token)
        }).then(function(){
          if(_this.$parent.checkLogin()){
            _this.$router.push({ name: 'MainPage' });
          }
      });

      event.preventDefault()
      return false
    }
  },
  beforeMount(){
    if(this.$parent.checkLogin()){
      this.$router.push({ name: 'MainPage' });
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
