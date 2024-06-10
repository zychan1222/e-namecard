docker-compose -f docker-compose.qas.yml build --no-cache
# aws ecr get-login-password --region ap-southeast-1 --profile skribble | docker login --username AWS --password-stdin 396192154208.dkr.ecr.ap-southeast-1.amazonaws.com
aws ecr get-login-password --region ap-southeast-1 | docker login --username AWS --password-stdin 396192154208.dkr.ecr.ap-southeast-1.amazonaws.com
docker push 396192154208.dkr.ecr.ap-southeast-1.amazonaws.com/skribble-learn-api-qas:1.0.4
