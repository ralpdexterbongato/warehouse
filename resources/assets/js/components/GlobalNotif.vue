<template lang="html">
  <li class="dropping-parent" v-on:click="dropIsActive=!dropIsActive">
    <h1 class="waves-effect waves-light">
      <i class="material-icons">notifications</i>
    </h1>
    <h2 class="number-of-unread z-depth-1">7</h2>
    <div class="notification-drop z-depth-1" v-on:click="dropIsActive=!dropIsActive" :class="[dropIsActive == true?'active':'']">
      <div class="notification-header">
        <h2> Notifications</h2>
      </div>
      <div class="notification-line-container">
        <div class="notification-drop-line">
          <div>
            <h2><i class="material-icons">person</i></h2>
          </div>
          <div class="drop-line-detail">
            <h5>Ralp Dexter Bongato</h5>
            <p>approved your MIRS request pproved your MIRS request</p><br>
            <i class="material-icons color-blue">thumb_up</i>
          </div>
        </div>
        <div class="divider">
        </div>
        <div class="notification-drop-line">
          <div>
            <h2><i class="material-icons">person</i></h2>
          </div>
          <div class="drop-line-detail">
            <h5>Ralp Dexter Bongato</h5>
            <p>approved your MIRS request pproved your MIRS request</p><br>
            <i class="material-icons color-red">close</i>
          </div>
        </div>
        <div class="divider">
        </div>
        <div class="notification-drop-line">
          <div>
            <h2><i class="material-icons">person</i></h2>
          </div>
          <div class="drop-line-detail">
            <h5>Ralp Dexter Bongato</h5>
            <p>approved your MIRS request pproved your MIRS request</p><br>
            <i class="material-icons color-red">close</i>
          </div>
        </div>
        <div class="divider">
        </div>
        <div class="notification-drop-line">
          <div>
            <h2><i class="material-icons">person</i></h2>
          </div>
          <div class="drop-line-detail">
            <h5>Ralp Dexter Bongato</h5>
            <p>approved your MIRS request pproved your MIRS request</p><br>
            <i class="material-icons color-red">close</i>
          </div>
        </div>
        <div class="divider">
        </div>
        <div class="notification-drop-line">
          <div>
            <h2><i class="material-icons">person</i></h2>
          </div>
          <div class="drop-line-detail">
            <h5>Ralp Dexter Bongato</h5>
            <p>approved your MIRS request pproved your MIRS request</p><br>
            <i class="material-icons color-red">close</i>
          </div>
        </div>
        <div class="divider">
        </div>
        <div class="notification-drop-line">
          <div>
            <h2><i class="material-icons">person</i></h2>
          </div>
          <div class="drop-line-detail">
            <h5>Ralp Dexter Bongato</h5>
            <p>approved your MIRS request pproved your MIRS request</p><br>
            <i class="material-icons color-red">close</i>
          </div>
        </div>
        <div class="divider">
        </div>
        <div class="notification-drop-line">
          <div>
            <h2><i class="material-icons">person</i></h2>
          </div>
          <div class="drop-line-detail">
            <h5>Ralp Dexter Bongato</h5>
            <p>approved your MIRS request pproved your MIRS request</p><br>
            <i class="material-icons color-red">close</i>
          </div>
        </div>
        <div class="divider">
        </div>
        <div class="notification-drop-line load-more">
          <div class="load-more-btn">
            <i class="material-icons">arrow_downward</i>
            Load
          </div>
        </div>
      </div>
      <div class="notification-footer">
      </div>
    </div>
  </li>
</template>

<script>
  export default {
    data () {
      return {
        dropIsActive:false,
      }
    },
    // props: [],
    methods: {

    },
  }
</script>
