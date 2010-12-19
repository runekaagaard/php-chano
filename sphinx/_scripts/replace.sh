#!/bin/bash
for fl in _build/html/*.html; do
    mv $fl $fl.old
    sed 's/="_static\//="static\//g' $fl.old > $fl
    rm -f $fl.old
done

