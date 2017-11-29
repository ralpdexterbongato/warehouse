<template lang="html">
  <div class="my-account-settings-vue">
    <div class="my-account-page-left">
    </div>
    <div class="my-account-page-middle">
      <h1 class="myacctitle"><i class="material-icons">settings</i> Your account</h1>
      <div class="myaccount-settings-table-container">
        <table>
          <tr>
            <th>Fullname</th><td>Ralp Dexter Bongato</td><td class="align-right"><i class="material-icons">security</i></td>
          </tr>
          <tr>
            <th>Position</th><td>Clerk</td><td class="align-right"><i class="material-icons">security</i></td>
          </tr>
          <tr>
            <th>Signature</th><td><img src="/DesignIMG/signature1.png"></td><td class="align-right"><i class="material-icons">security</i></td>
          </tr>
          <tr>
            <th>Username</th>
            <td v-if="usernameEdit==false">rdb2898</td>
            <td v-else><input type="text" autofocus></td>
            <td class="align-right">
              <span v-if="usernameEdit==false" v-on:click="usernameEdit=true">Edit</span>
              <span v-else>
                <button type="button" v-on:click="usernameEdit=false">
                  <i class="material-icons">check</i>
                </button>
                <button type="button" v-on:click="usernameEdit=false">
                  <i class="material-icons">close</i>
                </button>
              </span>
            </td>
          </tr>
          <tr class="contact-settings">
            <th>Contact</th><td>09105717885</td><td class="align-right"><span>Edit</span></td>
          </tr>
        </table>
      </div>
    </div>
    <div class="my-account-page-right">
    </div>
  </div>
</template>
<script>
  export default {
    data () {
      return {
        usernameEdit:false
      }
    },
    // computed: {},
    // methods: {},
  }
</script>
