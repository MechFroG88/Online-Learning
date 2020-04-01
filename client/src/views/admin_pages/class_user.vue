<template>
  <div id="_class_user">
    <div class="search-container">
      <h4 class="u-full-width">{{ $t('admin.filter') }}</h4>
      <div class="conditions row">
        <div class="year columns four">
          <label for="year">{{ $t('admin.year') }}</label>
          <select class="u-full-width" id="year" v-model="selected_year">
            <option value="">--</option>
            <option v-for="y in year" :key="y"
            :value="y">{{ y }}</option>
          </select>
        </div>
        <div class="class columns four">
          <label for="class">{{ $t('admin.class') }}</label>
          <select class="u-full-width" id="class" v-model="selected_class">
            <option value="">--</option>
            <option v-for="name in classname" :key="name"
            :value="name">{{ name }}</option>
          </select>
        </div>
        <div class="subject columns four">
          <label for="subject">{{ $t('admin.subjects') }}</label>
          <select class="u-full-width" id="subject" v-model="selected_subject">
            <option value="">--</option>
            <option v-for="subj in subjectArr" :key="subj.id"
            :value="subj.id">{{ lang == 'cn' ? subj.cn_name : subj.en_name }}</option>
          </select>
        </div>
      </div>
      <div class="button-group row">
        <button class="button-primary" @click="search">{{ $t('admin.search') }} <i class="icon-search"></i></button>
        <button class="button-error" @click="clear">{{ $t('admin.clear') }} <i class="icon-cross"></i></button>
      </div>
    </div>

    <hr>

    <div class="action-container">
      <dt-table
      :columns="classUser"
      :tableData="userArr"
      navbar="Name"
      hoverable
      title>
        <template slot="title">
          {{ $t('admin.teacher') }}
        </template>
        <template slot="class_user" slot-scope="{ data }">
          <template v-for="subj in data.class_user">
            {{ lang == 'cn' ? subj.class.cn_name : subj.class.en_name }}:
            {{ lang == 'cn' ? subj.subject.cn_name : subj.subject.en_name }}
            <br :key="(subj.subject_id+subj.class_id)*subj.subject_id" />
          </template>
          <template v-if="data.class_user.length == 0">
            --
          </template>
        </template>
        <template slot="action" slot-scope="{ data }">
          <button class="button-success" @click="selected_obj = data">
            {{ $t('timetable.select') }}
          </button>
        </template>
      </dt-table>

      <div class="action-panel">
        <h5 class="name">{{ lang == 'cn' ? selected_obj.cn_name : selected_obj.en_name }}</h5>
        <div class="add-subject row" v-if="selected_obj.id">
          <div class="columns six class">
            <label for="user_class">{{ $t('admin.class') }}</label>
            <select class="u-full-width" id="user_class" v-model="submit.class_id">
              <option :value="0">--</option>
              <option v-for="single_class in classArr" :key="single_class.id"
              :value="single_class.id">
                {{ lang == 'cn' ? single_class.cn_name : single_class.en_name }}
              </option>
            </select>
          </div>
          <div class="columns six subject">
            <label for="user_subject">{{ $t('admin.subjects') }}</label>
            <select class="u-full-width" id="user_subject" v-model="submit.subject_id">
              <option :value="0">--</option>
              <option v-for="subject in subjectArr" :key="subject.id"
              :value="subject.id">
                {{ lang == 'cn' ? subject.cn_name : subject.en_name }}
              </option>
            </select>
          </div>
          <button class="button-primary" @click="addSubj">{{ $t('table.add') }}</button>
        </div>
        <ul>
          <li v-for="subj in selected_obj.class_user" :key="subj.subject.id*(subj.subject.id + subj.class.id)">
            <button class="button-error delete"
            @click="deleteSubj(subj.id)"><i class="icon-cross"></i></button>
            <span>
              {{ lang == 'cn' ? subj.class.cn_name: subj.class.en_name }}: 
              {{ lang == 'cn' ? subj.subject.cn_name: subj.subject.en_name }}
            </span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { classUser } from '@/api/tableColumns';
import { createUser, getUsers, editUser, deleteUser, 
        addSubjectClass, deleteSubjectClass } from '@/api/users';
import { getAllClass } from '@/api/class';
import { getAllSubjects } from '@/api/subject';

import dtTable from '@/components/table';
export default {
  components: {
    dtTable
  },
  mounted() {
    this.year = this.$t('admin.years');
    getUsers().then((data) => {
      if (data.status == 200) {
        this.userArr = data.data.filter(el => el.type == 1);
        this.oriUserArr = this.userArr;
      }
    })
    getAllClass().then((data) => {
      if (data.status == 200) {
        this.classArr = data.data;
      }
    })
    getAllSubjects().then((data) => {
      if (data.status == 200) {
        this.subjectArr = data.data;
      }
    })
  },
  data: () => ({
    classUser,
    oriUserArr: [],
    userArr: [],
    classArr: [],
    subjectArr: [],
    selected_obj: {},
    year: [],
    classname: [],
    selected_year: '',
    selected_class: '',
    selected_subject: '',
    submit: {
      class_id: 0,
      subject_id: 0,
    }
  }),
  methods: {
    clear() {
      this.selected_year = '';
      this.selected_class = '';
      this.selected_subject = '';
      this.userArr = this.oriUserArr;
    },
    search() {
      this.userArr = this.oriUserArr
        .filter(el => el.class_user
          .some(elem => (elem.subject_id == this.selected_subject 
                        || this.selected_subject == '') 
                     && (elem.class[this.lang == 'cn' ? 'cn_name' : 'en_name'].includes(this.selected_year) 
                        || this.selected_year == '') 
                     && (elem.class[this.lang == 'cn' ? 'cn_name' : 'en_name'].includes(this.selected_class)
                        || this.selected_class == '')))
    },
    addSubj() {
      addSubjectClass({
        ...this.submit,
        user_id: this.selected_obj.id
      }).then((data) => {
        if (data.status == 200) this.update();
      }).catch((err) => {
        if (err.response)
          this.$notify({
            type: 'error',
            title: 'Error add subject',
            text: err.response.data
          })
        else
          this.$notify({
            type: 'error',
            title: 'Error add subject',
            text: err.message
          })
      })
    },
    deleteSubj(id) {
      deleteSubjectClass(id).then((data) => {
        if (data.status == 200) this.update();
      }).catch((err) => {
        if (err.response)
          this.$notify({
            type: 'error',
            title: 'Error delete',
            text: err.response.data
          })
        else
          this.$notify({
            type: 'error',
            title: 'Error delete',
            text: err.message
          })
      })
    },
    update() {
      getUsers().then((data) => {
        if (data.status == 200) {
          this.userArr = data.data.filter(el => el.type == 1);
          this.oriUserArr = this.userArr;
          this.search();
          this.selected_obj = this.oriUserArr
            .filter(el => el.id == this.selected_obj.id)[0];
        }
      })
    }
  },
  watch: {
    selected_year(val) {
      if (val == '') this.classname = [];
      else {
        this.classname = this.classArr
          .filter(el => {
            if (this.lang == 'cn') return el.cn_name.includes(val);
            return el.en_name.includes(val);
          })
          .map(el => {
            if (this.lang == 'cn') return el.cn_name[el.cn_name.length - 2];
            return el.en_name.substring(1);
          })
      }
    },
    lang() {
      this.year = this.$t('admin.years');
      this.clear();
    }
  },
  computed: {
    ...mapState({
      lang: 'lang'
    })
  }
}
</script>

<style lang="scss">

</style>