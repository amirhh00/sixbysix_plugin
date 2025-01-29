import { BlockEditProps } from "@wordpress/blocks";

type ExtractType<T> = T extends { type: infer U } ? U : never;

type KeysOfAttr<T> = keyof T;
type ValuesOfAttr<T, K extends KeysOfAttr<T>> = T[K];

export type ExtractTypesFromBlockJson<T> = {
	[K in KeysOfAttr<T>]: ExtractType<ValuesOfAttr<T, K>>;
};

type BlockEditVanila<T> = BlockEditProps<ExtractTypesFromBlockJson<T>>;
type BlockEdit<ATTR, EDIT = boolean> = EDIT extends true
	? BlockEditVanila<ATTR>
	: Partial<BlockEditVanila<ATTR>>;

export type ComponentProps<
	ATTR,
	OTHER = any,
	EDIT = boolean,
> = React.ComponentType<BlockEdit<ATTR, EDIT> & OTHER>;
