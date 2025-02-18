FROM php:8.1-cli
WORKDIR /var/www/html

# تثبيت Java وGradle وSDK
RUN apt-get update && apt-get install -y openjdk-17-jdk unzip wget
RUN wget https://services.gradle.org/distributions/gradle-8.2-bin.zip -O gradle.zip && \
    unzip gradle.zip -d /opt && \
    ln -s /opt/gradle-8.2/bin/gradle /usr/bin/gradle

# تثبيت أدوات Android
RUN mkdir -p /usr/local/android-sdk && \
    wget https://dl.google.com/android/repository/commandlinetools-linux-10406996_latest.zip -O sdk.zip && \
    unzip sdk.zip -d /usr/local/android-sdk && \
    rm sdk.zip

ENV ANDROID_HOME=/usr/local/android-sdk
ENV PATH=$ANDROID_HOME/cmdline-tools/latest/bin:$ANDROID_HOME/platform-tools:$ANDROID_HOME/emulator:$PATH

# قبول الرخص
RUN yes | sdkmanager --licenses
RUN sdkmanager "platform-tools" "platforms;android-34" "build-tools;34.0.0"

COPY . /var/www/html
CMD ["php", "-S", "0.0.0.0:8000"]
