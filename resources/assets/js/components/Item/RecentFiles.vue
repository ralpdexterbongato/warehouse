<template lang="html">
  <div class="big-user-box">
    <div class="big-user-box-title">
      <h3>Recent files</h3>
    </div>
    <div class="big-user-box-content">
        <div class="recent-files-box" v-for="mctData in UserRecentFiles.mctrecent">
          <div class="recent-file-box-top">
            <div class="creator-profile">
              {{Names.MCT[0].user.FullName.charAt(0)}}
            </div>
            <p v-if="Names.MCT!=null">{{Names.MCT[0].user.FullName}}</p>
            <h6>Received by</h6>
          </div>
          <div class="recent-file-box-bottom">
            <div class="transact-num-display">
              <p>
                <i class="material-icons" v-if="mctData.Status=='1'||mctData.IsRollBack=='0'">layers_clear</i>
                <i class="material-icons" v-else-if="mctData.Status=='0'">done_all</i>
                <i class="material-icons" v-else-if="mctData.Status==null">access_alarm</i>
              </p>
              <p>
                {{mctData.MCTNo}}<br>
                <span class="transact-label">Transaction No.</span>
              </p>
            </div>
            <div class="transact-opener-box">
              <div class="transact-opener-content">
                <a href="/mct-index-page">
                  <p><i class="material-icons">subject</i> MCT</p>
                </a>
              </div>
              <div class="transact-opener-content">
                <a :href="'/preview-mct-page-only/'+mctData.MCTNo">
                  <p><i class="material-icons">description</i> OPEN</p>
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="recent-files-box" v-for="mrtData in UserRecentFiles.mrtrecent">
          <div class="recent-file-box-top">
            <div class="creator-profile">
              {{Names.MRT[0].user.FullName.charAt(0)}}
            </div>
            <p v-if="Names.MRT!=null">{{Names.MRT[0].user.FullName}}</p>
            <h6>Returned by</h6>
          </div>
          <div class="recent-file-box-bottom">
            <div class="transact-num-display">
              <p>
                <i class="material-icons" v-if="mrtData.Status=='1'||mrtData.IsRollBack=='0'">layers_clear</i>
                <i class="material-icons" v-else-if="mrtData.Status=='0'">done_all</i>
                <i class="material-icons" v-else-if="mrtData.Status==null">access_alarm</i>
              </p>
              <p>
                {{mrtData.MRTNo}}<br>
                <span class="transact-label">Transaction No.</span>
              </p>
            </div>
            <div class="transact-opener-box">
              <div class="transact-opener-content">
                <a href="/mrt-index-page">
                  <p><i class="material-icons">subject</i> MRT</p>
                </a>
              </div>
              <div class="transact-opener-content">
                <a :href="'/mrt-preview-page/'+mrtData.MRTNo">
                  <p><i class="material-icons">description</i> OPEN</p>
                </a>
              </div>
            </div>
          </div>
        </div>
        <!-- break -->
        <div class="recent-files-box" v-for="mirsData in UserRecentFiles.mirsrecent">
          <div class="recent-file-box-top">
            <div class="creator-profile">
              {{Names.MIRS[0].user.FullName.charAt(0)}}
            </div>
            <p v-if="Names.MIRS!=null">{{Names.MIRS[0].user.FullName}}</p>
            <h6>Requested by</h6>
          </div>
          <div class="recent-file-box-bottom">
            <div class="transact-num-display">
              <p>
                <i class="material-icons" v-if="mirsData.Status=='1'">layers_clear</i>
                <i class="material-icons" v-else-if="mirsData.Status=='0'">done_all</i>
                <i class="material-icons" v-else-if="mirsData.Status==null">access_alarm</i>
              </p>
              <p>
                {{mirsData.MIRSNo}}<br>
                <span class="transact-label">Transaction No.</span>
              </p>
            </div>
            <div class="transact-opener-box">
              <div class="transact-opener-content">
                <a href="/mirs-index-page">
                  <p><i class="material-icons">subject</i> MIRS</p>
                </a>
              </div>
              <div class="transact-opener-content">
                <a :href="'/previewFullMIRS/'+mirsData.MIRSNo">
                  <p><i class="material-icons">description</i> OPEN</p>
                </a>
              </div>
            </div>
          </div>
        </div>
        <!-- break -->
        <div class="recent-files-box" v-for="rvData in UserRecentFiles.rvrecent">
          <div class="recent-file-box-top">
            <div class="creator-profile">
              {{Names.RV[0].user.FullName.charAt(0)}}
            </div>
            <p v-if="Names.RV!=null">{{Names.RV[0].user.FullName}}</p>
            <h6>Requested by</h6>
          </div>
          <div class="recent-file-box-bottom">
            <div class="transact-num-display">
              <p>
                <i class="material-icons" v-if="rvData.Status=='1'">layers_clear</i>
                <i class="material-icons" v-else-if="rvData.Status=='0'">done_all</i>
                <i class="material-icons" v-else-if="rvData.Status==null">access_alarm</i>
              </p>
              <p>
                {{rvData.RVNo}}<br>
                <span class="transact-label">Transaction No.</span>
              </p>
            </div>
            <div class="transact-opener-box">
              <div class="transact-opener-content">
                <a href="/RVindex">
                  <p><i class="material-icons">subject</i> RV</p>
                </a>
              </div>
              <div class="transact-opener-content">
                <a :href="'/RVfullview/'+rvData.RVNo">
                  <p><i class="material-icons">description</i> OPEN</p>
                </a>
              </div>
            </div>
          </div>
        </div>
        <!-- break -->
        <div class="recent-files-box"  v-for="rrData in UserRecentFiles.rrrecent">
          <div class="recent-file-box-top">
            <div class="creator-profile">
              {{Names.RR[0].user.FullName.charAt(0)}}
            </div>
            <p v-if="Names.RR!=null">{{Names.RR[0].user.FullName}}</p>
            <h6>Received by</h6>
          </div>
          <div class="recent-file-box-bottom">
            <div class="transact-num-display">
              <p>
                <i class="material-icons" v-if="rrData.Status=='1'||rrData.IsRollBack=='0'">layers_clear</i>
                <i class="material-icons" v-else-if="rrData.Status=='0'">done_all</i>
                <i class="material-icons" v-else-if="rrData.Status==null">access_alarm</i>
              </p>
              <p>
                {{rrData.RRNo}}<br>
                <span class="transact-label">Transaction No.</span>
              </p>
            </div>
            <div class="transact-opener-box">
              <div class="transact-opener-content">
                <a href="/RR-index">
                  <p><i class="material-icons">subject</i> RR</p>
                </a>
              </div>
              <div class="transact-opener-content">
                <a :href="'/RR-fullpreview/'+rrData.RRNo">
                  <p><i class="material-icons">description</i> OPEN</p>
                </a>
              </div>
            </div>
          </div>
        </div>
        <!-- break -->
        <div class="recent-files-box"  v-for="poData in UserRecentFiles.porecent">
          <div class="recent-file-box-top">
            <div class="creator-profile">
              {{Names.PO[0].user.FullName.charAt(0)}}
            </div>
            <p v-if="Names.PO!=null">{{Names.PO[0].user.FullName}}</p>
            <h6>Approved by</h6>
          </div>
          <div class="recent-file-box-bottom">
            <div class="transact-num-display">
              <p>
                <i class="material-icons" v-if="poData.Status=='1'">layers_clear</i>
                <i class="material-icons" v-else-if="poData.Status=='0'">done_all</i>
                <i class="material-icons" v-else-if="poData.Status==null">access_alarm</i>
              </p>
              <p>
                {{poData.PONo}}<br>
                <span class="transact-label">Transaction No.</span>
              </p>
            </div>
            <div class="transact-opener-box">
              <div class="transact-opener-content">
                <a href="/po-index-page">
                  <p><i class="material-icons">subject</i> PO</p>
                </a>
              </div>
              <div class="transact-opener-content">
                <a :href="'/po-full-preview/'+poData.PONo">
                  <p><i class="material-icons">description</i> OPEN</p>
                </a>
              </div>
            </div>
          </div>
        </div>
        <!-- break -->
        <div class="recent-files-box" v-for="mrdata in UserRecentFiles.mrrecent">
          <div class="recent-file-box-top">
            <div class="creator-profile" v-if="Names.MR[0]!=null">
              {{Names.MR[0].user.FullName.charAt(0)}}
            </div>
            <p v-if="Names.MR[0]!=null">{{Names.MR[0].user.FullName}}</p>
            <h6>Received by</h6>
          </div>
          <div class="recent-file-box-bottom">
            <div class="transact-num-display">
              <p>
                <i class="material-icons" v-if="mrdata.Status=='1'">layers_clear</i>
                <i  class="material-icons" v-else-if="mrdata.Status=='0'">done_all</i>
                <i class="material-icons" v-else-if="mrdata.Status==null">access_alarm</i>
              </p>
              <p>
                {{mrdata.MRNo}}<br>
                <span class="transact-label">Transaction No.</span>
              </p>
            </div>
            <div class="transact-opener-box">
              <div class="transact-opener-content">
                <a href="/mr-index-page">
                  <p><i class="material-icons">subject</i> MR</p>
                </a>
              </div>
              <div class="transact-opener-content">
                <a :href="'/full-preview-MR/'+mrdata.MRNo">
                  <p><i class="material-icons">description</i> OPEN</p>
                </a>
              </div>
            </div>
          </div>
        </div>
        <!-- empty mr -->
        <div class="recent-files-box" v-if="UserRecentFiles.mrrecent==''">
          <div class="recent-file-box-top">
            <div class="creator-profile">
              E
            </div>
            <p>No MR record yet</p>
            <h6>Empty</h6>
          </div>
          <div class="recent-file-box-bottom">
            <div class="transact-num-display">
              <p>
                <i  class="material-icons">block</i>
              </p>
              <p>
                00-0000<br>
                <span class="transact-label">Transaction No.</span>
              </p>
            </div>
            <div class="transact-opener-box">
              <div class="transact-opener-content">
                <a href="/mr-index-page">
                  <p><i class="material-icons">subject</i> MR</p>
                </a>
              </div>
              <div class="transact-opener-content">
                  <p><i class="material-icons">description</i> ...</p>
              </div>
            </div>
          </div>
        </div>
        <!-- empty mirs -->
        <div class="recent-files-box" v-if="UserRecentFiles.mirsrecent==''">
          <div class="recent-file-box-top">
            <div class="creator-profile">
              E
            </div>
            <p>No MIRS record yet</p>
            <h6>Empty</h6>
          </div>
          <div class="recent-file-box-bottom">
            <div class="transact-num-display">
              <p>
                <i  class="material-icons">block</i>
              </p>
              <p>
                00-0000<br>
                <span class="transact-label">Transaction No.</span>
              </p>
            </div>
            <div class="transact-opener-box">
              <div class="transact-opener-content">
                <a href="/mirs-index-page">
                  <p><i class="material-icons">subject</i> MIRS</p>
                </a>
              </div>
              <div class="transact-opener-content">
                  <p><i class="material-icons">description</i> ...</p>
              </div>
            </div>
          </div>
        </div>
        <!-- empty mct -->
        <div class="recent-files-box" v-if="UserRecentFiles.mctrecent==''">
          <div class="recent-file-box-top">
            <div class="creator-profile">
              E
            </div>
            <p>No MCT record yet</p>
            <h6>Empty</h6>
          </div>
          <div class="recent-file-box-bottom">
            <div class="transact-num-display">
              <p>
                <i  class="material-icons">block</i>
              </p>
              <p>
                00-0000<br>
                <span class="transact-label">Transaction No.</span>
              </p>
            </div>
            <div class="transact-opener-box">
              <div class="transact-opener-content">
                <a href="/mct-index-page">
                  <p><i class="material-icons">subject</i> MCT</p>
                </a>
              </div>
              <div class="transact-opener-content">
                  <p><i class="material-icons">description</i> ...</p>
              </div>
            </div>
          </div>
        </div>
        <!-- empty mrt -->
        <div class="recent-files-box" v-if="UserRecentFiles.mrtrecent==''">
          <div class="recent-file-box-top">
            <div class="creator-profile">
              E
            </div>
            <p>No MRT record yet</p>
            <h6>Empty</h6>
          </div>
          <div class="recent-file-box-bottom">
            <div class="transact-num-display">
              <p>
                <i  class="material-icons">block</i>
              </p>
              <p>
                00-0000<br>
                <span class="transact-label">Transaction No.</span>
              </p>
            </div>
            <div class="transact-opener-box">
              <div class="transact-opener-content">
                <a href="/mrt-index-page">
                  <p><i class="material-icons">subject</i> MRT</p>
                </a>
              </div>
              <div class="transact-opener-content">
                  <p><i class="material-icons">description</i> ...</p>
              </div>
            </div>
          </div>
        </div>
        <!-- rr empty -->
        <div class="recent-files-box" v-if="UserRecentFiles.rrrecent==''">
          <div class="recent-file-box-top">
            <div class="creator-profile">
              E
            </div>
            <p>No RR record yet</p>
            <h6>Empty</h6>
          </div>
          <div class="recent-file-box-bottom">
            <div class="transact-num-display">
              <p>
                <i  class="material-icons">block</i>
              </p>
              <p>
                00-0000<br>
                <span class="transact-label">Transaction No.</span>
              </p>
            </div>
            <div class="transact-opener-box">
              <div class="transact-opener-content">
                <a href="/RR-index">
                  <p><i class="material-icons">subject</i> RR</p>
                </a>
              </div>
              <div class="transact-opener-content">
                  <p><i class="material-icons">description</i> ...</p>
              </div>
            </div>
          </div>
        </div>
        <!-- RV empty -->
        <div class="recent-files-box" v-if="UserRecentFiles.rvrecent==''">
          <div class="recent-file-box-top">
            <div class="creator-profile">
              E
            </div>
            <p>No RV record yet</p>
            <h6>Empty</h6>
          </div>
          <div class="recent-file-box-bottom">
            <div class="transact-num-display">
              <p>
                <i  class="material-icons">block</i>
              </p>
              <p>
                00-0000<br>
                <span class="transact-label">Transaction No.</span>
              </p>
            </div>
            <div class="transact-opener-box">
              <div class="transact-opener-content">
                <a href="/RVindex">
                  <p><i class="material-icons">subject</i> RV</p>
                </a>
              </div>
              <div class="transact-opener-content">
                  <p><i class="material-icons">description</i> ...</p>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios';
  export default {
    data () { return {
        UserRecentFiles:[],
        Names:[],
      }
    },
    mounted () {
      if (this.user.Role!=1 && this.user.Role!=3 && this.user.Role != 4)
      {
        this.getRecentFiles();
      }
    },
    props: ['user'],
    computed: {},
    methods: {
      getRecentFiles()
      {
        var vm=this;
        axios.get(`/recent-files-get`).then(function(response)
        {
          vm.UserRecentFiles = response.data.recent[0];
          vm.Names = response.data;
          console.log(response);
        }).catch(function(error)
        {

        });
      }
    }
  }
</script>
