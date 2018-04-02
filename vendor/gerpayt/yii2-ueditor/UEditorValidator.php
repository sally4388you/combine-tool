<?php
namespace gerpayt\yii2_datetime_compare;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\validators\Validator;

/**
 * DateTimeCompareValidator compares the specified attribute value with another value.
 *
 * The value being compared with can be another attribute value
 * (specified via [[compareAttribute]]) or a constant (specified via
 * [[compareValue]]. When both are specified, the latter takes
 * precedence. If neither is specified, the attribute will be compared
 * with another attribute whose name is by appending "_repeat" to the source
 * attribute name.
 *
 * DateTimeCompareValidator supports different comparison operators, specified
 * via the [[operator]] property.
 *
 * @author Anushan Easwaramoorthy <EAnushan@hotmail.com>
 * @since 2.0
 */
class DateTimeCompareValidator extends Validator
{
    /**
     * @var string the name of the attribute to be compared with. When both this property
     * and [[compareValue]] are set, the latter takes precedence. If neither is set,
     * it assumes the comparison is against another attribute whose name is formed by
     * appending '_repeat' to the attribute being validated. For example, if 'password' is
     * being validated, then the attribute to be compared would be 'password_repeat'.
     * @see compareValue
     */
    public $compareAttribute;
    /**
     * @var mixed the constant value to be compared with. When both this property
     * and [[compareAttribute]] are set, this property takes precedence.
     * @see compareAttribute
     */
    public $nowValue;
    /**
     * @var string the operator for comparison. The following operators are supported:
     *
     * - `==`: check if two values are equal. The comparison is done is non-strict mode.
     * - `===`: check if two values are equal. The comparison is done is strict mode.
     * - `!=`: check if two values are NOT equal. The comparison is done is non-strict mode.
     * - `!==`: check if two values are NOT equal. The comparison is done is strict mode.
     * - `>`: check if value being validated is greater than the value being compared with.
     * - `>=`: check if value being validated is greater than or equal to the value being compared with.
     * - `<`: check if value being validated is less than the value being compared with.
     * - `<=`: check if value being validated is less than or equal to the value being compared with.
     */
    public $operator = '==';
    /**
     * @var string the user-defined error message. It may contain the following placeholders which
     * will be replaced accordingly by the validator:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     * - `{compareValue}`: the value or the attribute label to be compared with
     * - `{compareAttribute}`: the label of the attribute to be compared with
     */
    public $message;

    /**
     * @var integer The offset when compare.
     */
    public $offset = 0;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            throw new InvalidConfigException("Unknown operator: {$this->operator}");
        }
        $this->nowValue = Yii::$app->formatter->asDatetime(time());
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (is_array($value)) {
            $this->addError($model, $attribute, Yii::t('yii', '{attribute} is invalid.'));

            return;
        }

        $compareAttribute = $this->compareAttribute;
        $compareValue = $this->compareAttribute === null ? $this->nowValue : $model->$compareAttribute;
        //$compareLabel = $model->getAttributeLabel($compareAttribute);

        if (!$this->compareValues($this->operator, $value, $compareValue)) {
            $this->addError($model, $attribute, $this->message, [
                //'compareAttribute' => $compareLabel,
                'compareValue' => $compareValue,
            ]);
        }
    }

    /**
     * Compares two values with the specified operator.
     * @param string $operator the comparison operator
     * @param mixed $value the value being compared
     * @param mixed $compareValue another value being compared
     * @return boolean whether the comparison using the specified operator is true.
     */
    protected function compareValues($operator, $value, $compareValue)
    {
        $dateValue = new \DateTime($value);
        $dateCompareValue = new \DateTime();
        $compareStamp = strtotime($compareValue) + $this->offset;
        $dateCompareValue->setTimestamp($compareStamp);
        // TODO offset to ===

        switch ($operator) {
            case '==':
                return $dateValue == $dateCompareValue;
            case '===':
                return $value === $compareValue;
            case '!=':
                return $dateValue != $dateCompareValue;
            case '!==':
                return $value !== $compareValue;
            case '>':
                return $dateValue > $dateCompareValue;
            case '>=':
                return $dateValue >= $dateCompareValue;
            case '<':
                return $dateValue < $dateCompareValue;
            case '<=':
                return $dateValue <= $dateCompareValue;
            default:
                return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $options = [
            'operator' => $this->operator
        ];

        if ($this->compareAttribute === null) {
            $compareAttribute = $this->compareAttribute;
            $compareValue = $this->compareAttribute === null ? $this->nowValue : $model->$compareAttribute;
            $options['compareValue'] = $compareValue;
        } else {
            $compareAttribute = $this->compareAttribute;
            $compareValue = $model->getAttributeLabel($compareAttribute);
            $options['compareAttribute'] = Html::getInputId($model, $compareAttribute);
        }

        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        $options['offset'] = $this->offset;
        $options['message'] = Yii::$app->getI18n()->format($this->message, [
            'attribute' => $model->getAttributeLabel($attribute),
            'compareAttribute' => $compareValue,
            'compareValue' => $compareValue,
        ], Yii::$app->language);

        DateTimeCompareValidationAsset::register($view);

        return 'yii.validation.datetimecompare(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }
}
