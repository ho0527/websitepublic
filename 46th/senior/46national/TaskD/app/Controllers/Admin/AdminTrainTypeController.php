<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\TrainType;

/**
 * 後台車種管理。
 */
final class AdminTrainTypeController extends Controller
{
    /**
     * 車種列表。
     */
    public function index(): void
    {
        $this->requireAdmin();

        $this->renderAdmin('admin/train-types/index', [
            'title'      => '車種管理',
            'trainTypes' => TrainType::allOrdered(),
            'errors'     => Session::pullFlash('errors', []),
            'notice'     => Session::pullFlash('notice'),
        ]);
    }

    /**
     * 新增車種的表單。
     */
    public function create(): void
    {
        $this->requireAdmin();

        $this->renderAdmin('admin/train-types/form', [
            'title'     => '新增車種',
            'trainType' => null,
            'errors'    => Session::pullFlash('errors', []),
            'old'       => Session::pullFlash('old', []),
        ]);
    }

    /**
     * 寫入新車種。
     */
    public function store(): void
    {
        $this->requireAdmin();

        $input  = $this->collectInput();
        $errors = $this->validate($input, null);

        if ($errors !== []) {
            $this->redirectWithErrors('admin/train-types/create', $errors, $input);
        }

        TrainType::create([
            'name'     => $input['name'],
            'capacity' => (int) $input['capacity'],
        ]);

        Session::flash('notice', sprintf('已新增車種「%s」', $input['name']));
        $this->redirect('admin/train-types');
    }

    /**
     * 編輯車種的表單。
     */
    public function edit(string $id): void
    {
        $this->requireAdmin();

        $trainType = TrainType::find((int) $id);

        if ($trainType === null) {
            $this->redirectWithErrors('admin/train-types', ['查無此車種']);
        }

        $this->renderAdmin('admin/train-types/form', [
            'title'     => '編輯車種',
            'trainType' => $trainType,
            'errors'    => Session::pullFlash('errors', []),
            'old'       => Session::pullFlash('old', []),
        ]);
    }

    /**
     * 更新車種。
     */
    public function update(string $id): void
    {
        $this->requireAdmin();

        $trainType = TrainType::find((int) $id);

        if ($trainType === null) {
            $this->redirectWithErrors('admin/train-types', ['查無此車種']);
        }

        $input  = $this->collectInput();
        $errors = $this->validate($input, $trainType->id());

        if ($errors !== []) {
            $this->redirectWithErrors(sprintf('admin/train-types/%d/edit', $trainType->id()), $errors, $input);
        }

        $trainType->name     = $input['name'];
        $trainType->capacity = (int) $input['capacity'];
        $trainType->save();

        Session::flash('notice', sprintf('已更新車種「%s」', $trainType->name));
        $this->redirect('admin/train-types');
    }

    /**
     * 刪除車種；有列車使用時禁止刪除。
     */
    public function destroy(string $id): void
    {
        $this->requireAdmin();

        $trainType = TrainType::find((int) $id);

        if ($trainType === null) {
            $this->redirectWithErrors('admin/train-types', ['查無此車種']);
        }

        if ($trainType->isInUse()) {
            $this->redirectWithErrors('admin/train-types', [
                sprintf('車種「%s」仍有列車使用，無法刪除；請先刪除或改掉相關列車的車種', $trainType->name),
            ]);
        }

        $name = (string) $trainType->name;
        $trainType->delete();

        Session::flash('notice', sprintf('已刪除車種「%s」', $name));
        $this->redirect('admin/train-types');
    }

    /**
     * 收集表單輸入。
     *
     * @return array{name: string, capacity: string}
     */
    private function collectInput(): array
    {
        return [
            'name'     => $this->request->input('name', '') ?? '',
            'capacity' => $this->request->input('capacity', '') ?? '',
        ];
    }

    /**
     * 驗證車種資料。
     *
     * @param array{name: string, capacity: string} $input
     * @return array<int, string>
     */
    private function validate(array $input, ?int $exceptId): array
    {
        $errors = [];

        if ($input['name'] === '') {
            $errors[] = '請填寫車種名稱';
        } elseif (TrainType::nameExists($input['name'], $exceptId)) {
            $errors[] = sprintf('車種名稱「%s」已存在，請改用其他名稱', $input['name']);
        }

        if ($input['capacity'] === '' || !ctype_digit($input['capacity']) || (int) $input['capacity'] < 1) {
            $errors[] = '乘客承載量必須是大於 0 的整數';
        }

        return $errors;
    }
}
