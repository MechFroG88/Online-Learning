<template>
  <div id="_timetable">
    <template v-for="(period, ind) in periods">
      <div class="period-cell filler" :key="period.start_time"
      v-if="ind == 0 ? false : period.start_time != periods[ind-1].end_time">
        <div class="time">
          <span>{{ periods[ind-1].end_time }}</span>
        </div>
        <div class="info">
          {{ $t('timetable.empty') }}
        </div>
      </div>
      <div class="period-cell" :key="period.id">
        <div class="time">
          <span>{{ period.start_time }}</span>
        </div>
        <div class="info">
          <div class="occupied" 
          v-if="fc = filtered_choice.filter(el => el.period_id == period.id)[0]"
          :style="{
            'background-color': getColor(fc.subject_id),
            'color': getFontColor(getColor(fc.subject_id))
          }">
            <div class="teacher-name">
              <!-- {{ lang == 'cn' ? fc.cn_name : fc.en_name }} -->
              {{ fc.cn_name }}
            </div>
            
            <button class="button button-primary edit" 
            @click="edit(period, 
            filtered_choice.filter(el => el.period_id == period.id)[0])"
            v-if="fc.user_id == user.id"
            style="margin-top: .5rem;">
              {{ $t('timetable.modify') }}
            </button>
          </div>
          <div class="empty" v-else>
            <button class="button select" @click="select(period)">
              {{ $t('timetable.select') }}
            </button>
          </div>
        </div>
      </div>
      <div class="period-cell end-filler" :key="period.end_time"
      v-if="ind == periods.length - 1">
        <div class="time">
          <span>{{ period.end_time }}</span>
        </div>
        <div class="info"></div>
      </div>
    </template>
  </div>
</template>

<script>
import moment from 'moment';
import { mapState } from 'vuex';

export default {
  props: {
    date: {
      type: Date,
      required: true
    },
    periods: {
      type: Array,
      required: true
    },
    choices: {
      type: Array,
      default: () => [],
    }
  },
  mounted() {
    this.filtered_choice = this.choices.filter(el => 
      moment(el.date).format() == moment(this.date, 'DD/MM/YYYY').format());
  },
  data: () => ({
    infos: [],
    filtered_choice: [],
    selected_period: {},
  }),
  methods: {
    getColor(subject) {
      function hashCode(str) {
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
          hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        return hash;
      } 

      function intToRGB(i) {
        var hex = ((i>>24)&0xFF).toString(16) +
                ((i>>16)&0xFF).toString(16) +
                ((i>>8)&0xFF).toString(16) +
                (i&0xFF).toString(16);
                
        hex += '000000';
        return hex.substring(0, 6);
      }
      return '#'+intToRGB(hashCode(subject.toString().repeat(10000000)));
    },
    getFontColor(bgColor) {
      let red   = parseInt('0x'+bgColor.substring(0,2)),
          green = parseInt('0x'+bgColor.substring(2,4)),
          blue  = parseInt('0x'+bgColor.substring(4));

      let luminance = ( 0.299 * red + 0.587 * green + 0.114 * blue)/255;
      if (luminance > 0.5) return '#000';
      return '#fff';
    },
    select(period) {
      this.$emit('selected', this.date, period);
      this.$parent.$parent.$parent.$refs.modal.active = true;
    },
    edit(period, choice) {
      this.$emit('edit', this.date, period, choice);
      this.$parent.$parent.$parent.$refs.modal.active = true;
    }
  },
  computed: {
    ...mapState({ 
      lang: 'lang', 
      user: 'user' 
    }),
  }
}
</script>

<style lang="scss">

</style>