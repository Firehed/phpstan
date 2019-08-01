<?php declare(strict_types = 1);

namespace PHPStan\Type\Generic;

use PHPStan\Type\ErrorType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeTraverser;

class TemplateTypeHelper
{

	/**
	 * Replaces template types with standin types
	 */
	public static function resolveTemplateTypes(Type $type, TemplateTypeMap $standins): Type
	{
		return TypeTraverser::map($type, static function (Type $type, callable $traverse) use ($standins): Type {
			if ($type instanceof TemplateType && !$type->isArgument()) {
				$newType = $standins->getType($type->getName()) ?? $type;

				if ($newType instanceof ErrorType) {
					$newType = $type->getBound();
				}

				return $traverse($newType);
			}

			return $traverse($type);
		});
	}

	public static function resolveToBounds(Type $type): Type
	{
		return TypeTraverser::map($type, static function (Type $type, callable $traverse): Type {
			if ($type instanceof TemplateType) {
				return $traverse($type->getBound());
			}

			return $traverse($type);
		});
	}

	/**
	 * Switches all template types to their argument strategy
	 */
	public static function toArgument(Type $type): Type
	{
		return TypeTraverser::map($type, static function (Type $type, callable $traverse): Type {
			if ($type instanceof TemplateType) {
				return $traverse($type->toArgument());
			}

			return $traverse($type);
		});
	}

}
