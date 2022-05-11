<?php
namespace lib;

use League\Flysystem\Filesystem;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use think\exception\ValidateException;
use think\facade\Filesystem as FacadeFilesystem;

class Excel
{
    /**
     * 导入Excel
     * 
     * @param string $filename
     * @return array|string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public static function import($filename = "")
    {
        try {
            // 截取后缀
            $fileExtendName = substr(strrchr($filename, '.'), 1);
            // 有Xls和Xlsx格式两种
            if ($fileExtendName == 'xlsx') {
                $objReader = IOFactory::createReader('Xlsx');
            } else {
                $objReader = IOFactory::createReader('Xls');
            }
            // 设置文件为只读
            $objReader->setReadDataOnly(TRUE);
            // 读取文件，tp6默认上传的文件，在runtime的相应目录下，可根据实际情况自己更改
            $objPHPExcel = $objReader->load(public_path() . $filename);
            //excel中的第一张sheet
            $sheet = $objPHPExcel->getSheet(0);
            // 取得总行数
            $highestRow = $sheet->getHighestRow();
            // 取得总列数
            $highestColumn = $sheet->getHighestColumn();
            Coordinate::columnIndexFromString($highestColumn);
            $lines = $highestRow - 1;
            if ($lines <= 0) {
                return "数据为空数组";
            }
            // 直接取出excle中的数据
            $data = $objPHPExcel->getActiveSheet()->toArray();
            // 删除第一个元素（表头）
            array_shift($data);
            //删除文件
            unlink(public_path() . $filename);
            // 返回结果
            return $data;
        } catch (ValidateException $e) {
            return $e->getMessage();
        }
    }

    // 导出
    public static function export($header = [], $type = true, $data = [], $fileName = "Akali", $width = [])
    {
        // 实例化类
        $preadsheet = new Spreadsheet();
        // 创建sheet
        $sheet = $preadsheet->getActiveSheet();
        // 循环设置表头数据
        foreach ($header as $k => $v) {
            $sheet->setCellValue($k, $v);
        }
        // 生成数据
        $sheet->fromArray($data, null, "A2");

        // 样式设置
        // 默认宽度
        $sheet->getDefaultColumnDimension()->setWidth(12);
        // 默认高度
        $sheet->getDefaultRowDimension()->setRowHeight(16);
        
        // 自定义宽度
        if ($width) {
            foreach($width as $key => $value){
                $sheet->getColumnDimension($value['alphabet'])->setWidth($value['width']);
            }
        }

        // 设置下载与后缀
        if ($type) {
            header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            $type = "Xlsx";
            $suffix = "xlsx";
        } else {
            header("Content-Type:application/vnd.ms-excel");
            $type = "Xls";
            $suffix = "xls";
        }
        ob_end_clean();//清楚缓存区
        // 激活浏览器窗口
        header("Content-Disposition:attachment;filename=$fileName.$suffix");
        //缓存控制
        header("Cache-Control:max-age=0");
        // 调用方法执行下载
        $writer = IOFactory::createWriter($preadsheet, $type);
        // 数据流
        $writer->save("php://output");
    }

    /**
     * 导入excel
     *
     * @param  Request $request
     * @return \think\Response
     */
    public function importExcel(Request $request)
    {
        // 接收文件上传信息
        $files = $request->file("myfile");

        // 调用类库，读取excel中的内容
        $data = Excel::import($files);
        
        // 返回二维数组
        return $data;
    }
    
    /**
     * 导出excel使用方法如下
     *
     * @param  Request $request
     * @return \think\Response
     */
    public function exportExcel()
    {
        /**********************   参数接收   **********************/
        $ids = input('ids/a');
        empty($ids) and akali('请勾选报名');
        $ids = array_unique($ids);

        $data = CollegeJoin::with(['college', 'order'])->whereIn('id', $ids)->select();

        $excelData = [];
        foreach ($data as $key => $value) {
            $excelData[$key]['sequence']       = $key + 1;
            $excelData[$key]['order_sn']       = $value['order']['order_sn'];
            $excelData[$key]['title']          = $value['college']['title'];
            $excelData[$key]['name']           = $value['name'];
            $excelData[$key]['phone']          = $value['phone'];
            $excelData[$key]['company']        = $value['company'];
            $excelData[$key]['demand']         = $value['demand'];
            $excelData[$key]['number']         = $value['order']['number'];
            $excelData[$key]['pay_status']     = $value['order']['pay_status'] == 1 ? '已支付' : '未支付';
            $excelData[$key]['sms_status']     = $value['sms_status'] == 1 ? '已通知' : '未通知';
            $excelData[$key]['connect_status'] = $value['connect_status'] == 1 ? '已联系' : '未联系';
            $excelData[$key]['create_time']    = $value['create_time'];
        }

        // 设置表格的表头数据
        $header = [
            "A1" => "序号",
            "B1" => "订单号",
            "C1" => "课程名称",
            "D1" => "姓名",
            "E1" => "联系电话",
            "F1" => "公司名称",
            "G1" => "需求/问题",
            "H1" => "数量",
            "I1" => "支付状态",
            "J1" => "短信通知",
            "K1" => "联系状态",
            "L1" => "报名时间",
        ];

        // 设置表格的行列宽
        $width = [
            ['alphabet' => 'A', 'width' => 8],
            ['alphabet' => 'B', 'width' => 24],
            ['alphabet' => 'C', 'width' => 24],
            ['alphabet' => 'D', 'width' => 12],
            ['alphabet' => 'E', 'width' => 14],
            ['alphabet' => 'F', 'width' => 30],
            ['alphabet' => 'G', 'width' => 30],
            ['alphabet' => 'H', 'width' => 8],
            ['alphabet' => 'I', 'width' => 12],
            ['alphabet' => 'J', 'width' => 12],
            ['alphabet' => 'K', 'width' => 12],
            ['alphabet' => 'L', 'width' => 20],
        ];

        // halt($header);
        // 保存文件的类型
        $type = false;
        // 设置下载文件保存的名称
        $fileName = "课程报名-" . date('Y-m-d-His');
        // 调用方法导出excel
        Excel::export($header, $type, $excelData, $fileName, $width);
    }
}


