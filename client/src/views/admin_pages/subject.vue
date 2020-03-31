<template>
  <div id="_subject">
    <dt-table
    :columns="subject"
    navbar="subject name"
    :tableData="subjectArr"
    hoverable
    title>
      <template slot="title">
        {{ $t('admin.subjects') }}
        <button class="button-primary" @click="openSubject()">{{ $t('table.add') }}</button>
      </template>
      <template slot="action" slot-scope="{ data }">
        <button class="button button-primary" @click="openSubject(data, false)">更改</button>
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

    <modal ref="editSubject">
      <form @submit.prevent="submitSubject">
        <div class="u-full-width cn_name">
          <label for="cn_name">{{ $t('modal.cn_name') }}: </label>
          <input class="u-full-width" type="text" id="cn_name" 
          v-model="edit.subject.cn_name">
        </div>
        <div class="u-full-width en_name">
          <label for="en_name">{{ $t('modal.en_name') }}: </label>
          <input class="u-full-width" type="text" id="en_name" 
          v-model="edit.subject.en_name">
        </div>
        <div class="u-full-width week">
          <label for="week">{{ $t('modal.week') }}: </label>
          <input class="u-full-width" type="number" id="week" 
          v-model="edit.subject.week">
        </div>
        <div class="u-full-width day">
          <label for="day">{{ $t('modal.day') }}: </label>
          <input class="u-full-width" type="number" id="day" 
          v-model="edit.subject.day">
        </div>

        <input type="submit" class="button button-primary" 
        style="margin-top: 1rem;"
        :value="$t('modal.submit')">
      </form>
    </modal>
  </div>
</template>

<script>
import { subject } from '@/api/tableColumns';
import { createSubject, getAllSubjects, editSubject, deleteSubject } from '@/api/subject';

import dtTable from '@/components/table';
import modal from '@/components/modal';
export default {
  components: {
    dtTable,
    modal
  },
  mounted() {
    getAllSubjects().then((data) => {
      if (data.status == 200) {
        this.subjectArr = data.data;
      }
    })
  },
  data: () => ({
    subject,
    subjectArr: [],
    isAdd: false,
    edit: {
      id: 0,
      subject: {
        cn_name: '',
        en_name: '',
        day: 0,
        week: 0,
      }
    },
    delete: {
      id: 0
    }
  }),
  methods: {
    openSubject(data, isAdd = true) {
      this.isAdd = isAdd;
      if (!this.isAdd) this.edit.id = data.id;
      this.edit.subject = data || this.edit.subject;
      this.$refs.editSubject.active = true;
    },
    submitSubject() {
      if (this.isAdd) {
        createSubject(this.edit.subject).then((data) => {
          if (data.status == 200) {
            this.$refs.editSubject.active = false;
            getAllSubjects().then((data) => {
              if (data.status == 200) {
                this.subjectArr = data.data;
                this.edit.subject = {
                  cn_name: '',
                  en_name: '',
                  day: 0,
                  week: 0,
                };
                this.$nextTick(this.$forceUpdate);
              }
            })
          }
        })
      }
      else {
        editSubject(this.edit.subject, this.edit.id).then((data) => {
          if (data.status == 200) {
            this.$refs.editSubject.active = false;
            getAllSubjects().then((data) => {
              if (data.status == 200) {
                this.subjectArr = data.data;
                this.edit.subject = {
                  cn_name: '',
                  en_name: '',
                  day: 0,
                  week: 0,
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
      deleteSubject(this.delete.id).then((data) => {
        if (data.status == 200) {
          this.$refs.confirmModal.active = false;
          getAllSubjects().then((data) => {
            if (data.status == 200) {
              this.subjectArr = data.data;
              this.$nextTick(this.$forceUpdate);
            }
          })
        }
      })
    }
  }
}
</script>

<style lang="scss">

</style>