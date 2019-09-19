<?php
use Migrations\AbstractMigration;
use Cake\Auth\DefaultPasswordHasher;

class CreateAdminSeedMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        $faker = \Faker\Factory::create();
        $populator = new Faker\ORM\CakePHP\Populator($faker);
        
        $populator->addEntity('Users', 1, [
            'first_name' => 'Hugo Daniel',
            'last_name' => 'Ramirez',
            'email' => 'hramirez@ministeriopublico.gov.py',
            'password' => function () {
                $hasher = new DefaultPasswordHasher();
                return $hasher->hash('hugo2019');
                //return 'secret';
            },
            'role' => 'admin',
            'active' => 1,
            'created' => function () use ($faker) {
                return $faker->dateTimeBetween($startDate = 'now', $endDate = 'now');
            },
            'modified' => function () use ($faker) {
                return $faker->dateTimeBetween($startDate = 'now', $endDate = 'now');
            }
        ]);
        $populator->execute();
    }
}
