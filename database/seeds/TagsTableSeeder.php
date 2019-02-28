<?php

use App\Enums\TagType;
use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tags')->truncate();

        $data = [
            [
                'type' => TagType::DESIRE,
                'name' => [
                    '聞き上手',
                    '話上手',
                    'カラオケ上手',
                    'お酒が飲める',
                    '学生',
                    '社会人',
                ],
            ],
            [
                'type' => TagType::SITUATION,
                'name' => [
                    'プライベート',
                    '接待',
                    'わいわい',
                    'しっぽり',
                    'さくっと',
                    '終電まで',
                    '朝まで',
                    'カラオケ',
                    'クラブ',
                    'ランチ',
                    'バー',
                    '映画',
                    'ショッピング',
                ],
            ],
        ];

        $importData = [];

        foreach ($data as $typeSet) {
            $type = $typeSet['type'];

            foreach ($typeSet['name'] as $name) {
                $importData[] = compact('name', 'type');
            }
        }

        DB::table('tags')->insert($importData);
    }
}
