<template>
  <div id="_class">
    <dt-table
    :columns="classes"
    navbar="class name"
    :tableData="classArr"
    hoverable
    title>
      <template slot="title">
        {{ $t('admin.class') }}
        <button class="button-primary" @click="openClass()">{{ $t('table.add') }}</button>
      </template>
      <template slot="action" slot-scope="{ data }">
        <button class="button button-primary" @click="openClass(data, false)">{{ $t('admin.edit') }}</button>
        <button class="button button-error" @click="confirmDelete(data.id)"
        style="margin-left: .5rem;">{{ $t('admin.delete') }}</button>
      </template>
    </dt-table>

    <!-- modals -->
    <modal ref="confirmModal" class="delete-modal">
      <div class="modal-body">
        <h5>{{ $t('modal.confirmDelete') }}</h5>
        <button class="button button-error" @click="doDelete"
        style="float: right; margin-top: 3rem;">
          {{ $t('modal.confirm') }}
        </button>
      </div>
    </modal>

    <modal ref="editClass">
      <form @submit.prevent="submitClass">
        <div class="u-full-width cn_name">
          <label for="class_cn_name">{{ $t('modal.cn_name') }}: </label>
          <input class="u-full-width" type="text" id="class_cn_name" 
          v-model="edit.class.cn_name">
        </div>
        <div class="u-full-width en_name">
          <label for="class_en_name">{{ $t('modal.en_name') }}: </label>
          <input class="u-full-width" type="text" id="class_en_name" 
          v-model="edit.class.en_name">
        </div>
        <div class="u-full-width code">
          <label for="class_code">{{ $t('modal.code') }}: </label>
          <input class="u-full-width" type="text" id="class_code" 
          v-model="edit.class.code">
        </div>
        <div class="u-full-width class_teacher">
          <label for="class_teacher">{{ $t('modal.class_teacher') }}</label>
          <v-select 
            :options="users" 
            :reduce="user => user.id" 
            v-model="edit.class.user_id"
            :label="lang == 'cn' ? 'cn_name' : 'en_name'"></v-select>
        </div>

        <input type="submit" class="button button-primary" 
        style="margin-top: 1rem;"
        :value="$t('modal.submit')">
      </form>
    </modal>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { classes } from '@/api/tableColumns';
import { getUsers } from '@/api/users';
import { createClass, getAllClass, editClass, deleteClass } from '@/api/class';

import dtTable from '@/components/table';
import modal from '@/components/modal';
export default {
  components: {
    dtTable,
    modal
  },
  mounted() {
    getAllClass().then((data) => {
      if (data.status == 200) {
        this.classArr = data.data;
      }
    })
    getUsers().then((data) => {
      if (data.status == 200) {
        this.users = data.data;
      }
    })
  },
  data: () => ({
    classes,
    classArr: [],
    users: [],
    isAdd: false,
    edit: {
      id: 0,
      class: {
        cn_name: '',
        en_name: '',
        code : '',
        user_id: null
      },
    },
    delete: {
      id: 0
    },
    class_obj: {
      cn_name: '',
      en_name: '',
      code : '',
      user_id: null
    }
  }),
  methods: {
    openClass(data, isAdd = true) {
      this.isAdd = isAdd;
      if (!this.isAdd) this.edit.id = data.id;
      this.edit.class = data || this.edit.class;
      this.$refs.editClass.active = true;
    },
    submitClass() {
      if (this.isAdd) {
        createClass(this.edit.class).then((data) => {
          if (data.status == 200) {
            this.$refs.editClass.active = false;
            getAllClass().then((data) => {
              if (data.status == 200) {
                this.classArr = data.data;
                this.edit.class = {...this.class_obj};
                this.$nextTick(this.$forceUpdate);
              }
            })
          }
        }).catch((err) => {
          if (err.response)
            this.$notify({
              type: 'error',
              title: 'Error add',
              text: err.response.data
            })
          else
            this.$notify({
              type: 'error',
              title: 'Error add',
              text: err.message
            })
        })
      }
      else {
        editClass(this.edit.class, this.edit.id).then((data) => {
          if (data.status == 200) {
            this.$refs.editClass.active = false;
            getAllClass().then((data) => {
              if (data.status == 200) {
                this.classArr = data.data;
                this.edit.class = {...this.class_obj};
                this.$nextTick(this.$forceUpdate);
              }
            })
          }
        }).catch((err) => {
          if (err.response)
            this.$notify({
              type: 'error',
              title: 'Error edit',
              text: err.response.data
            })
          else
            this.$notify({
              type: 'error',
              title: 'Error edit',
              text: err.message
            })
        })
      }
    },
    confirmDelete(id) {
      this.$refs.confirmModal.active = true;
      this.delete.id = id;
    },
    doDelete() {
      deleteClass(this.delete.id).then((data) => {
        if (data.status == 200) {
          this.$refs.confirmModal.active = false;
          getAllClass().then((data) => {
            if (data.status == 200) {
              this.classArr = data.data;
              this.$nextTick(this.$forceUpdate);
            }
          })
        }
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