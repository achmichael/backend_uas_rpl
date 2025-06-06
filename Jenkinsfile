pipeline {
    agent any

    environment {
        IMAGE_NAME = 'backend_uas_rpl-frankenphp'
        CONTAINER_NAME = 'backend_frankenphp'
        DOCKER_BUILDKIT = 1
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                sh 'docker build -t $IMAGE_NAME .'
            }
        }

        stage('Stop & Remove Existing Container') {
            steps {
                sh '''
                    docker stop $CONTAINER_NAME || true
                    docker rm $CONTAINER_NAME || true
                '''
            }
        }

        stage('Run New Container') {
            steps {
                sh '''
                    docker run -d --name $CONTAINER_NAME -p 8000:8000 $IMAGE_NAME
                '''
            }
        }
    }
}
