<?php

namespace App\Exports;

use App\Choice;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DateTime;

class ChoiceExport implements FromArray, WithHeadings, ShouldAutoSize
{

    public function headings(): array
    {
        return [
            '日期',
            '班级',
            '科目',
            '老师',
            '时间（开始）',
            '时间（结束）',
            '教学媒介',
            '远程教学链接(link)',
            '远程教学ID',
            '远程教学密码',
            '其他详情',
        ];
    }

    public function array(): array
    {
        $data = [];
        $choices = Choice::with('period')->get();
        foreach ($choices as $choice){
            $row = [];
            array_push($row,$choice->date);
            array_push($row,$choice->class_user->class->cn_name);
            array_push($row,$choice->class_user->subject->cn_name);
            array_push($row,$choice->class_user->user->cn_name);
            array_push($row,DateTime::createFromFormat('H:i:s',$choice->period->start_time)->format('h:i A'));
            array_push($row,DateTime::createFromFormat('H:i:s',$choice->period->end_time)->format('h:i A'));
            array_push($row,$choice->method);
            array_push($row,$choice->link);
            array_push($row,$choice->streamId);
            array_push($row,$choice->streamPassword);
            array_push($row,$choice->description);

            array_push($data,$row);
        }
        return $data;
    }
}
