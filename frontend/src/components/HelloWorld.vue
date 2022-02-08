<template>
  <div class="hello">
    <h1>{{ msg }}</h1>
    <form action="">
      <p>
      <label>
        Введите свой ник
        <input name="name">
      </label>
      </p>
      <p>
        <input type="submit" name="create" value="Создать комнату">
      </p>
      <p>
      -- ЛИБО ---
      </p>
      <p>
        <label>
          Введите номер комнаты
          <input name="name">
        </label>
      </p>
      <p>
        <input type="submit" v-on:click="greet" name="enter" value="Войти">
      </p>
    </form>
  </div>
</template>

<script>
export default {
  name: 'HelloWorld',
  data () {
    return {
      msg: 'Welcome to Your Vue.js App 234'
    }
  },
  methods: {
    greet: function (event) {
      // `this` внутри методов указывает на экземпляр Vue
      alert('Привет !')
      // `event` — нативное событие DOM
      if (event) {
        alert(event.target.tagName)
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h1, h2 {
  font-weight: normal;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  display: inline-block;
  margin: 0 10px;
}

a {
  color: #42b983;
}
</style>
