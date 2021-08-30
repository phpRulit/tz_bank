<template>
  <div id="app">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <span class="navbar-brand">Банк</span>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item" v-for="(route, key) in routes" v-bind:key="route.path">
            <router-link :to="{ name : route.path }" :key="key" class="nav-link">{{route.name}}</router-link>
          </li>
        </ul>
      </div>
    </nav>
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  data () {
    return {
      routes: [
        { name: 'Анкета', path: 'form'},
        { name: 'Новые ограничения', path: 'new.rules'},
      ],
    }
  }
}
</script>


<style>
@import "~bootstrap/dist/css/bootstrap.min.css";
</style>


