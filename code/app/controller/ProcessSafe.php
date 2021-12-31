<?php


namespace app\controller;

use think\facade\Db;
use think\facade\View;
use think\Request;

class ProcessSafe extends Common
{
    public $typeArr = ['黑盒扫描','白盒审计','专项利用','其他','信息收集'];

    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if ($search) {
            $where[] = ['key|value|note', 'like', "%{$search}%"];
        }
        $type = $request->param('type','');
        if ($type !== '') {
            $type = array_search($type,$this->typeArr);
            $where[] = ['type','=',$type];
        }
        $list = Db::table('process_safe')->where($where)->order("id", 'desc')->paginate($pageSize);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }


    // 添加管理员
    public function add()
    {
        if (request()->isPost()) {
            $data['key'] = getParam('key');
            $data['value'] = getParam('value');
            $data['status'] = getParam('status');
            $data['note'] = getParam('note');
            //添加
            if (Db::name('process_safe')->insert($data)) {
                $this->success('添加成功', 'index');
            } else {
                $this->error('添加失败');
            }
        } else {
            ;
            return View::fetch('add');
        }
    }

    public function edit()
    {
        $id = getParam('id');
        if (request()->isPost()) {
            $data['key'] = getParam('key');
            $data['value'] = getParam('value');
            $data['status'] = getParam('status');
            $data['note'] = getParam('note');
            if (Db::name('process_safe')->where('id', $id)->update($data)) {
                return redirect(url('index'));
            } else {
                $this->error('信息修改失败');
            }
        } else {
            $map[] = ['id', '=', $id];
            $data['info'] = Db::name('process_safe')->where($map)->find();
            return View::fetch('edit', $data);
        }
    }

    public function del()
    {
        $id = getParam('id');
        if (Db::name('process_safe')->where('id', $id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    public function showProcess()
    {
        $cmd = "ps -ef |grep php | grep -v def | grep -v grep";

        exec($cmd,$info);
        $data['info'] = $info;
        return View::fetch('show_process', $data);
    }

    public function kill(){
        $pid = getParam('pid','intval');

        $cmd = "kill -9 {$pid}";

        exec($cmd);

        return redirect($_SERVER['HTTP_REFERER']);
    }
}