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
        <button class="button button-primary" @click="openClass(data, false)">更改</button>
        <button class="button button-error" @click="confirmDelete(data.id)"
        style="margin-left: .5rem;">删除</button>
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

        <input type="submit" class="button button-primary" 
        style="margin-top: 1rem;"
        :value="$t('modal.submit')">
      </form>
    </modal>
  </div>
</template>

<script>
import { classes } from '@/api/tableColumns';
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
  },
  data: () => ({
    classes,
    classArr: [],
    isAdd: false,
    edit: {
      id: 0,
      class: {
        cn_name: '',
        en_name: '',
      },
    },
    delete: {
      id: 0
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
                this.edit.class = {
                  cn_name: '',
                  en_name: ''
                };
                this.$nextTick(this.$forceUpdate);
              }
            })
          }
        })
      }
      else {
        editClass(this.edit.class, this.edit.id).then((data) => {
          if (data.status == 200) {
            this.$refs.editClass.active = false;
            getAllClass().then((data) => {
              if (data.status == 200) {
                this.classArr = data.data;
                this.edit.class = {
                  cn_name: '',
                  en_name: ''
                };
                this.$nextTick(this.$forceUpdate);
              }
            })
          }
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
      })
    }
  },
}
</script>

<style lang="scss">

</style>