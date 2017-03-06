echo "Testing Project PHP Code"
echo "#################"
echo "Controllers:"
for file in controller/*
do
    php -l "$file" 2>&1 > /dev/null
done
echo ""
echo "Views:"
for file in view/*
do
    php -l "$file" 2>&1 > /dev/null
done
echo ""
echo "Models: "
for file in model/*
do
    php -l "$file" 2>&1 > /dev/null
done
