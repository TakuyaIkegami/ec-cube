<?php

use Codeception\Util\Fixtures;
use Page\Admin\CsvSettingsPage;
use Page\Admin\CustomerManagePage;
use Page\Admin\CustomerEditPage;

/**
 * @group admin
 * @group admin02
 * @group customer
 * @group ea5
 */
class EA05CustomerCest
{
    public function _before(\AcceptanceTester $I)
    {
        // すべてのテストケース実施前にログインしておく
        // ログイン後は管理アプリのトップページに遷移している
        $I->loginAsAdmin();
    }

    public function _after(\AcceptanceTester $I)
    {
    }

    public function customer_検索(\AcceptanceTester $I)
    {
        $I->wantTo('EA0501-UC01-T01 検索');


        $CustomerListPage = CustomerManagePage::go($I);

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();

        $CustomerListPage->検索($customer->getEmail());
        $I->see('検索結果：1件が該当しました', CustomerManagePage::$検索結果メッセージ);
    }

    public function customer_検索結果なし(\AcceptanceTester $I)
    {
        $I->wantTo('EA0501-UC01-T02 検索 結果なし');
        $faker = Fixtures::get('faker');
        $email = microtime(true).'.'.$faker->safeEmail;

        CustomerManagePage::go($I)
            ->検索($email);

        $I->see('検索条件に合致するデータが見つかりませんでした', CustomerManagePage::$検索結果_結果なしメッセージ);
    }

    public function customer_会員登録(\AcceptanceTester $I)
    {
        $I->wantTo('EA0502-UC01-T02(& UC01-T02) 会員登録');
        $faker = Fixtures::get('faker');
        $email = microtime(true).'.'.$faker->safeEmail;

        $CustomerRegisterPage = CustomerEditPage::go($I)
            ->入力_姓('testuser')
            ->入力_名('testuser')
            ->入力_セイ('テストユーザー')
            ->入力_メイ('テストユーザー')
            ->入力_都道府県(['27' => '大阪府'])
            ->入力_郵便番号1('530')
            ->入力_郵便番号2('0001')
            ->入力_市区町村名('大阪市北区梅田2-4-9')
            ->入力_番地_ビル名('ブリーゼタワー13F')
            ->入力_Eメール($email)
            ->入力_電話番号1('111')
            ->入力_電話番号2('111')
            ->入力_電話番号3('111')
            ->入力_パスワード('password')
            ->入力_パスワード確認('password');

        $findPluginByCode = Fixtures::get('findPluginByCode');
        $Plugin = $findPluginByCode('MailMagazine');
        if ($Plugin) {
            $I->amGoingTo('メルマガプラグインを発見したため、メルマガを購読します');
            $I->click('#admin_customer_mailmaga_flg_0');
        }

        $CustomerRegisterPage->登録();
        /* ブラウザによるhtml5のエラーなのでハンドリング不可 */
        $I->see('会員情報を保存しました。', CustomerEditPage::$登録完了メッセージ);    }

    public function customer_会員登録_必須項目未入力(\AcceptanceTester $I)
    {
        $I->wantTo('EA0502-UC01-T02 会員登録_必須項目未入力');

        CustomerEditPage::go($I)->登録();

        $I->seeElement(['css' => '#admin_customer_name_name01:invalid']); // 姓がエラー
        $I->dontSeeElement(CustomerEditPage::$登録完了メッセージ);
    }

    public function customer_会員編集(\AcceptanceTester $I)
    {
        $I->wantTo('EA0502-UC02-T01 会員編集');

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();

        $CustomerListPage = CustomerManagePage::go($I)
            ->検索($customer->getEmail());

        $I->see('検索結果：1件が該当しました', CustomerManagePage::$検索結果メッセージ);

        $CustomerListPage->一覧_編集(1);

        $CustomerRegisterPage = CustomerEditPage::at($I)
            ->入力_姓('testuser-1');

        $findPluginByCode = Fixtures::get('findPluginByCode');
        $Plugin = $findPluginByCode('MailMagazine');
        if ($Plugin) {
            $I->amGoingTo('メルマガプラグインを発見したため、メルマガを購読します');
            $I->click('#admin_customer_mailmaga_flg_0');
        }

        $CustomerRegisterPage->登録();
        $I->see('会員情報を保存しました。', CustomerEditPage::$登録完了メッセージ);

        $CustomerRegisterPage
            ->入力_姓('')
            ->登録();
    }

    public function customer_会員編集_必須項目未入力(\AcceptanceTester $I)
    {
        $I->wantTo('EA0502-UC02-T02 会員編集_必須項目未入力');

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();

        $CustomerListPage = CustomerManagePage::go($I)
            ->検索($customer->getEmail());

        $I->see('検索結果：1件が該当しました' ,CustomerManagePage::$検索結果メッセージ);

        $CustomerListPage->一覧_編集(1);

        CustomerEditPage::at($I)
            ->入力_姓('')
            ->登録();

        $I->seeElement(['css' => '#admin_customer_name_name01:invalid']);
        $I->dontSeeElement(CustomerEditPage::$登録完了メッセージ);
    }

    public function customer_会員削除(\AcceptanceTester $I)
    {
        $I->getScenario()->incomplete('未実装：会員削除は未実装');
        $I->wantTo('EA0501-UC03-T01 会員削除');

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();

        $CustomerManagePage = CustomerManagePage::go($I)
            ->検索($customer->getEmail());

        $CustomerManagePage->一覧_削除(1);
        $I->acceptPopup();

        $I->see('検索条件に合致するデータが見つかりませんでした', CustomerManagePage::$検索結果_結果なしメッセージ);
    }

    public function customer_会員削除キャンセル(\AcceptanceTester $I)
    {
        $I->getScenario()->incomplete('未実装：会員削除は未実装');
        $I->wantTo('EA0501-UC03-T02 会員削除キャンセル');

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();

        $CustomerManagePage = CustomerManagePage::go($I)
            ->検索($customer->getEmail());

        $CustomerIdForNotDel = $CustomerManagePage->一覧_会員ID(1);
        $CustomerManagePage->一覧_削除(1);
        $I->cancelPopup();

        $I->assertEquals($CustomerIdForNotDel, $CustomerManagePage->一覧_会員ID(1));
    }

    /**
     * @env firefox
     * @env chrome
     */
    public function customer_CSV出力(\AcceptanceTester $I)
    {
        $I->wantTo('EA0501-UC05-T01 CSV出力');

        $findCustomers = Fixtures::get('findCustomers');
        CustomerManagePage::go($I)
            ->検索()
            ->CSVダウンロード();

        $CustomerCSV = $I->getLastDownloadFile('/^customer_\d{14}\.csv$/');
        $I->assertEquals(count($findCustomers()) + 1, count(file($CustomerCSV)));
    }

    public function customer_CSV出力項目設定(\AcceptanceTester $I)
    {
        $I->wantTo('EA0501-UC04-T01 CSV出力項目設定');


        CustomerManagePage::go($I)
            ->検索()
            ->CSV出力項目設定();

        CsvSettingsPage::at($I);
        $value = $I->grabValueFrom(CsvSettingsPage::$CSVタイプ);
        $I->assertEquals('2', $value);
    }

    public function customer_仮会員メール再送(\AcceptanceTester $I)
    {
        $I->getScenario()->incomplete('未実装：仮会員メール再送は未実装');
        $I->wantTo('EA0501-UC06-T01(& UC06-T02) 仮会員メール再送');

        $I->resetEmails();

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer(null, false);
        $BaseInfo = Fixtures::get('baseinfo');

        CustomerManagePage::go($I)
            ->検索($customer->getEmail())
            ->一覧_仮会員メール再送(1);
        $I->acceptPopup();
        $I->wait(10);

        $I->seeEmailCount(2);
        foreach (array($customer->getEmail(), $BaseInfo->getEmail01()) as $email) {
            $I->seeInLastEmailSubjectTo($email, '会員登録のご確認');
            $I->seeInLastEmailTo($email, $customer->getName01().' '.$customer->getName02().' 様');
        }
    }
}
