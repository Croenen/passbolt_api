<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Resources Model
 *
 * @property \App\Model\Table\SecretsTable|\Cake\ORM\Association\HasMany $Secrets
 * @property \App\Model\Table\UsersResourcesPermissionsTable|\Cake\ORM\Association\HasMany $UsersResourcesPermissions
 *
 * @method \App\Model\Entity\Resource get($primaryKey, $options = [])
 * @method \App\Model\Entity\Resource newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Resource[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Resource|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Resource patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Resource[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Resource findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ResourcesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('resources');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Secrets', [
            'foreignKey' => 'resource_id'
        ]);
        $this->hasMany('UsersResourcesPermissions', [
            'foreignKey' => 'resource_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->uuid('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('name')
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->scalar('username')
            ->allowEmpty('username');

        $validator
            ->dateTime('expiry_date')
            ->allowEmpty('expiry_date');

        $validator
            ->scalar('uri')
            ->allowEmpty('uri');

        $validator
            ->scalar('description')
            ->allowEmpty('description');

        $validator
            ->boolean('deleted')
            ->requirePresence('deleted', 'create')
            ->notEmpty('deleted');

        $validator
            ->uuid('created_by')
            ->requirePresence('created_by', 'create')
            ->notEmpty('created_by');

        $validator
            ->uuid('modified_by')
            ->requirePresence('modified_by', 'create')
            ->notEmpty('modified_by');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['username']));

        return $rules;
    }

    /**
     * Build the query that fetches data for resource index
     *
     * @param Query $query a query instance
     * @param array $options options
     * @throws Exception if no role is specified
     * @return Query
     */
    public function findIndex(Query $query, array $options)
    {
        // Options must contain a role
        if (!isset($options['role'])) {
            throw new Exception(__('User table findIndex should have a role set in options.'));
        }

        // Default associated data
        $query->contain([
        ]);

        // Filter out guests, inactive and deleted users
        $where = [
            'Resources.deleted' => false
        ];
        $query->where($where);

        return $query;
    }
}