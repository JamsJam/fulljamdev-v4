<?php

namespace App\Application\Page\Element\Attribute;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class SafeHtmlAttributesValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof SafeHtmlAttributes) {
            throw new UnexpectedTypeException($constraint, SafeHtmlAttributes::class);
        }

        if (!is_array($value)) {
            return;
        }

        foreach ($value as $attribute => $attributeValue) {
            if (!is_string($attribute) || !$this->isAllowed($attribute) || (!is_scalar($attributeValue) && null !== $attributeValue)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ attribute }}', (string) $attribute)
                    ->addViolation();
            }
        }
    }

    private function isAllowed(string $attribute): bool
    {
        $attribute = strtolower($attribute);

        return in_array($attribute, ['id', 'target', 'rel', 'title'], true)
            || str_starts_with($attribute, 'aria-')
            || str_starts_with($attribute, 'data-');
    }
}
